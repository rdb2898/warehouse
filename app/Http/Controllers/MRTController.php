<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MRTMaster;
use App\MCTMaster;
use Carbon\Carbon;
use App\MaterialsTicketDetail;
use Session;
use DB;
class MRTController extends Controller
{
    public function CreateMRT(Request $request)
    {
      $MTDetails=MaterialsTicketDetail::where('MTType', 'MCT')->where('MTNo', $request->MCTNo)->get();
      $MCTdata=MCTMaster::where('MCTNo',$request->MCTNo)->get(['Particulars','AddressTo']);
      return view('Warehouse.MRTformView',compact('MTDetails','MCTdata'));
    }
    public function summaryMRT()
    {
      return view('Warehouse.MRT-summary');
    }

    public function StoreMRT(Request $request)
    {
      $this->MRTValidator($request);
      if (!empty(Session::get('MCTSelected')))
      {
        $year=Carbon::now()->format('y');
        $MRTNum=MRTMaster::orderBy('id','DESC')->take(1)->value('MRTNo');
        if (count($MRTNum)>0)
        {
          $numOnly=substr($MRTNum,'3');
          $numOnly = (int)$numOnly;
          $newID=$numOnly + 1;
          $MRTincremented= $year . '-' . sprintf("%04d",$newID);

        }else
        {
         $MRTincremented=  $year . '-' . sprintf("%04d",'1');
        }
        $mrtDB=new MRTMaster;
        $mrtDB->MRTNo=$MRTincremented;
        $mrtDB->MCTNo =$request->MCTNo;
        $mrtDB->ReturnDate = Carbon::now();
        $mrtDB->Particulars =$request->Particulars;
        $mrtDB->AddressTo= $request->AddressTo;
        $mrtDB->Returnedby = $request->Returnedby;
        $mrtDB->Receivedby = $request->Receivedby;
        $mrtDB->Remarks = $request->Remarks;
        $mrtDB->save();

        foreach (Session::get('MCTSelected') as $MRTitem)
        {
          $MTdetails=MaterialsTicketDetail::orderBy('created_at','DESC')->where('ItemCode', $MRTitem->ItemCode)->take(1)->get();
          $qty=(float)$MRTitem->Summary;
          $price=(float)$MTdetails[0]->CurrentCost;
          $MRTammount=$qty * $price;
          $currentQty=$qty + $MTdetails[0]->CurrentQuantity;
          $currentAmnt= $currentQty * $price;

          $newMTDetail=new MaterialsTicketDetail;
          $newMTDetail->ItemCode = $MRTitem->ItemCode;
          $newMTDetail->MTType = 'MRT';
          $newMTDetail->MTNo =$MRTincremented;
          $newMTDetail->AccountCode=$MTdetails[0]->AccountCode;
          $newMTDetail->UnitCost=$MTdetails[0]->UnitCost;
          $newMTDetail->Quantity=$MRTitem->Summary;
          $newMTDetail->Unit=$MTdetails[0]->Unit;
          $newMTDetail->Amount=$MRTammount;
          $newMTDetail->CurrentCost=$MTdetails[0]->CurrentCost;
          $newMTDetail->CurrentQuantity=$currentQty;
          $newMTDetail->CurrentAmount=$currentAmnt;
          $newMTDetail->save();
          Session::forget('MCTSelected');
        }
        return redirect()->route('MIRSgridview');
      }else
      {
        return redirect()->back()->with('message', 'Ooops you forgot to add the items returned');
      }
    }
    public function addToSession(Request $request)
    {
      if (Session::has('MCTSelected'))
      {
        foreach (Session::get('MCTSelected') as $Alreadyhere)
        {
          if ($Alreadyhere->ItemCode === $request->ItemCode)
          {
            return redirect()->back()->with('message', 'Sorry this item is already added');
          }
        }
      }
      $MRTselected = array('ItemCode'=>$request->ItemCode ,'Description'=>$request->Description,'Unit'=>$request->Unit,'Summary'=>$request->Summary );
      $MRTselected = (object)$MRTselected;
      Session::push('MCTSelected',$MRTselected);
      return redirect()->back();
    }
    public function deletePartSession($id)
    {
      if(Session::has('MCTSelected'))
      {
        $items=(array)Session::get('MCTSelected');
        foreach ($items as $key=>$item)
        {
          if ($item->ItemCode == $id)
          {
            unset($items[$key]);
          }
        }
        Session::put('MCTSelected',$items);
        return redirect()->back();
      }
    }
    public function MRTValidator($request)
    {
      return $this->validate($request,[
       'MCTNo'=>'required|unique:MRTMaster',
      ]);
    }
    public function MRTSearchdate(Request $request)
    {
      $datesearch=$request->monthInput;
      /*$MRTitems= DB::table("MaterialsTicketDetails")
  	    ->select(DB::raw("SUM(Quantity) as totalQty"),DB::raw("ItemCode as ItemCode"))
        ->where('MTType', 'MRT')->whereDate('created_at','LIKE',date($datesearch).'%')
        ->orderBy("ItemCode")
  	    ->groupBy(DB::raw("ItemCode"))
  	    ->get();
        return $MRTitems;*/
      $itemsummary=MaterialsTicketDetail::where('MTType', 'MRT')->whereDate('created_at','LIKE',date($datesearch).'%')->orderBy('ItemCode','DESC')->get();
        return view('Warehouse.MRT-summary',compact('itemsummary'));
    }
}
