<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use App\MIRSDetail;
use App\MIRSMaster;
use App\MaterialsTicketDetail;
use DB;
use App\User;
use App\MCTMaster;
use Auth;
use Redis;
use App\Jobs\SendMIRSNotification;
use App\Jobs\NewApprovedMIRSJob;
use App\Jobs\MIRSApprovalReplacer;
use App\Signatureable;
class MIRSController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function MIRScreate()
  {
    $GenMan=User::orderBy('id','DESC')->where('Role','2')->whereNotNull('IsActive')->take(1)->get(['FullName','id']);
    $mymanager=User::where('id',Auth::user()->Manager)->get(['FullName']);
    return view('Warehouse.MIRS.MIRSCreate',compact('mymanager','GenMan'));
  }
  public function fetchSessionMIRS()
  {
      return Session::get('ItemSelected');
  }
  public function addingSessionItem(Request $request)
  {
    $this->SessionValidator($request);
    $MTDetails=MaterialsTicketDetail::orderBy('id','DESC')->where('ItemCode',$request->ItemCode)->value('CurrentQuantity');
    if($MTDetails >=$request->Quantity)
    {
      $itemselected =[
      'ItemCode' => $request->ItemCode,'Particulars' => $request->Particulars,'Unit' => $request->Unit,'Remarks'=>$request->Remarks,'Quantity' => $request->Quantity,
      ];
      if (Session::has('ItemSelected'))
      {
        foreach (Session::get('ItemSelected') as $selected)
        {
          if ($selected->ItemCode == $request->ItemCode) {
            return response()->json(['error'=>'This Item has been added already']);
          }
        }
      }
        $itemselected = (object)$itemselected;
        Session::push('ItemSelected',$itemselected);
    }else
    {
      return response()->json(['error'=>'Quantity stock is not enough for your request']);
    }
  }
  public function deletePartSession($id)
  {
    if(Session::has('ItemSelected'))
    {
      $items=(array)Session::get('ItemSelected');
      foreach ($items as $key=>$item)
      {
        if ($item->ItemCode == $id)
        {
          unset($items[$key]);
        }
      }
      Session::put('ItemSelected',$items);
    }
  }
  public function SessionValidator($request)
  {
    return $this->validate($request,[
      'Quantity' => 'Required|Integer|min:1',
      'Remarks' => 'max:50',
    ]);
  }
  public function StoringMIRS(Request $request)
  {
    $this->storingMIRSValidator($request);
    if (count(Session::get('ItemSelected'))>0)
    {
      $date=Carbon::now();
      $year=Carbon::now()->format('y');
        $lastinserted=MIRSMaster::orderBy('MIRSNo','DESC')->take(1)->value('MIRSNo');
        if (count($lastinserted)>0)
        {
          $numOnly=substr($lastinserted,'3');
          $numOnly = (int)$numOnly;
          $soloID=$numOnly + 1;
          $incremented = $year .'-' . sprintf("%04d",$soloID);
        }else
        {
            $incremented = $year . '-' . sprintf("%04d",'1');
        }
      $master=new MIRSMaster;
      $master->MIRSNo = $incremented;
      $master->Purpose =$request->Purpose;
      $master->MIRSDate = $date;
      $master->save();

      $ApproveReplacer=User::whereNotNull('IfApproveReplacer')->take(1)->get(['id']);
      if (!empty($ApproveReplacer[0]))
      {
        $forSignatureDB = array(
          array('user_id'=>Auth::user()->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'PreparedBy'),
          array('user_id'=>Auth::user()->Manager,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'RecommendedBy'),
          array('user_id'=>$request->Approvedby,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'ApprovedBy'),
          array('user_id'=>$ApproveReplacer[0]->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'ApprovalReplacer')
        );
      }else
      {
        $forSignatureDB = array(
          array('user_id'=>Auth::user()->id,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'PreparedBy'),
          array('user_id'=>Auth::user()->Manager,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'RecommendedBy'),
          array('user_id'=>$request->Approvedby,'signatureable_id'=>$incremented,'signatureable_type'=>'App\MIRSMaster','Signature'=>Null,'SignatureType'=>'ApprovedBy')
        );
      }
      Signatureable::insert($forSignatureDB);

      $selectedITEMS=Session::get('ItemSelected');
      $selectedITEMS = (array)$selectedITEMS;
      $forMIRSDetailtbl = array();
      foreach ($selectedITEMS as $items)
      {
        $forMIRSDetailtbl[] = array('MIRSNo' => $incremented ,'ItemCode'=>$items->ItemCode,'Particulars'=>$items->Particulars,'Remarks'=>$items->Remarks,'Quantity'=>$items->Quantity,'QuantityValidator'=>$items->Quantity,'Unit'=>$items->Unit);
      }
      MIRSDetail::insert($forMIRSDetailtbl);

      Session::forget('ItemSelected');
      return ['redirect'=>route('full-mirs',[$incremented])];
    }else
    {
      return ['error'=>'items cannot be empty'];
    }
  }
  public function storingMIRSValidator($request)
  {
    $this->validate($request,[
      'Purpose'=>'required',
      'Approvedby'=>'required',
    ]);
  }
  public function fullMIRSview($id)
  {
    $MIRSNumber=['MIRSNo'=>$id];
    $MIRSNumber=json_encode($MIRSNumber);
    return view('Warehouse.MIRS.MIRSpreview',compact('MIRSNumber'));
  }
  public function fetchFullMIRSData($id)
  {
    $QuantityValidator=MIRSDetail::where('MIRSNo',$id)->get(['QuantityValidator']);
    $unclaimed=$QuantityValidator->sum('QuantityValidator');
    $MIRSDetail=MIRSDetail::where('MIRSNo', $id)->get();
    $MIRSMaster=MIRSMaster::with('users')->where('MIRSNo', $id)->get();
    $MCTNumber=MCTMaster::where('MIRSNo', $id)->value('MCTNo');
    $response = array(
      'unclaimed' => $unclaimed,
      'MIRSDetail'=>$MIRSDetail,
      'MIRSMaster'=>$MIRSMaster,
      'MCTNumber'=>$MCTNumber,
   );
   return response()->json($response);
  }
  public function searchMIRSNoAndFetch(Request $request)
  {
    return MIRSMaster::with('users')->where('MIRSNo','LIKE','%'.$request->MIRSNo.'%')->orderBy('MIRSNo','DESC')->paginate(10,['MIRSNo','Purpose','MIRSDate']);
  }
  public function MIRSIndexPage()
  {
    return view('Warehouse.MIRS.MIRS-index');
  }
  public function DeniedMIRS($id)
  {
    Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('user_id', Auth::user()->id)->update(['Signature'=>'1']);
    MIRSMaster::where('MIRSNo', $id)->update(['Status'=>'1']);
  }
  public function MIRSSignature($id)
  {
    $PreparedId=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'PreparedBy')->get(['user_id']);
    $RecommendId=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'RecommendedBy')->get(['user_id']);
    $GMId=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovedBy')->get(['user_id']);
    $ApprovalReplacerId=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovalReplacer')->get(['user_id']);

    if ($PreparedId[0]->user_id==Auth::user()->id)
    {
      Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'PreparedBy')->update(['Signature'=>'0']);
      MIRSMaster::where('MIRSNo', $id)->update(['SignatureTurn'=>'1']);
      $newmirs = array('tobeNotify'=>Auth::user()->Manager);
      $newmirs=(object)$newmirs;
      $job = (new SendMIRSNotification($newmirs))
                  ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }

    if ($RecommendId[0]->user_id==Auth::user()->id)
    {
      Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ManagerReplacer')->delete();
      Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'RecommendedBy')->update(['Signature'=>'0']);
      MIRSMaster::where('MIRSNo', $id)->update(['SignatureTurn'=>'2']);
      $tobeNotifycontainer= array('tobeNotify' =>$GMId[0]->user_id);
      $tobeNotifycontainer=(object)$tobeNotifycontainer;
      $job = (new SendMIRSNotification($tobeNotifycontainer))
                    ->delay(Carbon::now()->addSeconds(5));
      dispatch($job);

      if ($ApprovalReplacerId[0]!=null)
      {
        $tobeNotifycontainer  = array('tobeNotify' =>$ApprovalReplacerId[0]->user_id);
        $tobeNotifycontainer=(object)$tobeNotifycontainer;
        $job = (new SendMIRSNotification($tobeNotifycontainer))
                    ->delay(Carbon::now()->addSeconds(5));
        dispatch($job);
      }
    }
    if ($GMId[0]->user_id==Auth::user()->id)
    {
      Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovalReplacer')->delete();
      Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovedBy')->update(['Signature'=>'0']);
      MIRSMaster::where('MIRSNo', $id)->update(['Status'=>'0','SignatureTurn'=>'3']);
      $RequisitionerMobile=User::where('id',$PreparedId[0]->user_id)->get(['Mobile']);
      $NotifData = array('RequisitionerMobile' =>$RequisitionerMobile[0]->Mobile ,'MIRSNo'=>$id);
      $NotifData=(object)$NotifData;
      $job=(new NewApprovedMIRSJob($NotifData))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
  }
  public function mirsRequestcheck()
  {
    $user=User::find(Auth::user()->id);
    $myrequestMIRS = $user->MIRSSignatureTurn()->paginate(10);
    return view('Warehouse.MIRS.myMIRSrequest',compact('myrequestMIRS'));
  }
  public function readyForMCT()
  {
    $readyformct=MIRSMaster::orderBy('MIRSNo','DESC')->where('Status', '0')->whereNull('WithMCT')->paginate(10);
    return view('Warehouse.MIRS.MIRSReadyList',compact('readyformct'));
  }
  public function CancelApproveMIRSinBehalf($id)
  {
    Signatureable::where('signatureable_id',$id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovalReplacer')->delete();
  }
  public function AcceptApprovalRequest($id)
  {
    Signatureable::where('signatureable_id',$id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovalReplacer')->update(['Signature'=>'0']);
    MIRSMaster::where('MIRSNo', $id)->update(['Status'=>'0','SignatureTurn'=>'3']);
    $PreparedId=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'PreparedBy')->get(['user_id']);
    $GMId=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovedBy')->get(['user_id']);

    $RequisitionerMobile=User::where('id',$PreparedId[0]->user_id)->get(['Mobile']);
    $GMMobile=User::where('id',$GMId[0]->user_id)->get(['Mobile']);
    $NotifData = array('RequisitionerMobile' =>$RequisitionerMobile[0]->Mobile ,'MIRSNo'=>$id,'GMMobile'=>$GMMobile[0]->Mobile,'ApprovalReplacer'=>Auth::user()->FullName);
    $NotifData=(object)$NotifData;
    $job=(new MIRSApprovalReplacer($NotifData))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function fetchAllManager()
  {
    return User::whereNotNull('IsActive')->where('Role',0)->get(['FullName','id']);
  }
  public function SendRequestManagerReplacer($id,Request $request)
  {
    if (empty($request->ManagerReplacerID))
    {
      return ['error'=>'Required'];
    }
    $signatureDB=new Signatureable;
    $signatureDB->user_id = $request->ManagerReplacerID;
    $signatureDB->signatureable_id = $id;
    $signatureDB->signatureable_type = 'App\MIRSMaster';
    $signatureDB->SignatureType= 'ManagerReplacer';
    $signatureDB->save();
    $notify = array('tobeNotify' =>$request->ManagerReplacerID);
    $notify=(object)$notify;
    $job = (new SendMIRSNotification($notify))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function CancelRequestManagerReplacer($id)
  {
    Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ManagerReplacer')->delete();
  }
  public function SignatureManagerReplacer($id)
  {
    Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ManagerReplacer')->update(['Signature'=>'0']);
    MIRSMaster::where('MIRSNo', $id)->update(['SignatureTurn'=>'2']);
    $GMId=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovedBy')->get(['user_id']);
    $ApprovalReplacerId=Signatureable::where('signatureable_id', $id)->where('signatureable_type', 'App\MIRSMaster')->where('SignatureType', 'ApprovalReplacer')->get(['user_id']);
    if (!empty($ApprovalReplacerId[0])) {
      $notifyname = array('tobeNotify' =>$ApprovalReplacerId[0]->user_id);
      $notifyname=(object)$notifyname;
      $job = (new SendMIRSNotification($notifyname))->delay(Carbon::now()->addSeconds(5));
      dispatch($job);
    }
    $GMid = array('tobeNotify' =>$GMId[0]->user_id);
    $GMid=(object)$GMid;
    $job = (new SendMIRSNotification($GMid))->delay(Carbon::now()->addSeconds(5));
    dispatch($job);
  }
  public function MIRSNotification()
  {
    $user=User::find(Auth::user()->id);
    $myrequestMIRS = $user->MIRSSignatureTurn()->count();
    $response = ['MIRSrequest' =>$myrequestMIRS];
    return response()->json($response);
  }
  public function newlyApprovedMIRSCount()
  {
    $readyformct=MIRSMaster::orderBy('MIRSNo','DESC')->where('Status', '0')->whereNull('WithMCT')->count();
    $response=['NewlyApprovedMIRS'=>$readyformct];
    return response()->json($response);
  }
}
