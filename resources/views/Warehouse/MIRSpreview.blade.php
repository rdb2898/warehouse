@extends('layouts.master')
@section('title')
  Printable| MIRS
@endsection
@section('body')
  <div class="printable-view-container">
    <div class="printable-paper">
      <div class="print-btn-container">
        @if (isset($MIRSMaster[0]->MIRSNo) && isset($MIRSDetail[0]->MIRSNo))
        <form class="download-form" action="{{route('mirs-download')}}" method="post">
          {{csrf_field()}}
          <input type="text" name="MIRSNo" value="{{$MIRSMaster[0]->MIRSNo}}" style="display:none">
          <button type="submit">PDF <i class="fa fa-file-pdf-o"></i></button>
        </form>
      @else
        <h1>NO CURRENT MIRS RESULT</h1>
      @endif
      @if (($MIRSMaster[0]->PreparedSignature==null)||($MIRSMaster[0]->RecommendSignature==null)||($MIRSMaster[0]->ApproveSignature==null))
        @if (($MIRSMaster[0]!=null)&&($MIRSMaster[0]->ApproveSignature!=Auth::user()->Signature)&&($MIRSMaster[0]->RecommendSignature != Auth::user()->Signature)&&($MIRSMaster[0]->PreparedSignature != Auth::user()->Signature))
          @if (($MIRSMaster[0]->Preparedby == Auth::user()->Fname . ' '. Auth::user()->Lname)||($MIRSMaster[0]->Approvedby == Auth::user()->Fname . ' '. Auth::user()->Lname)||($MIRSMaster[0]->Recommendedby == Auth::user()->Fname . ' '. Auth::user()->Lname))
            <div class="middle-status">
              <form class="Accept" action="{{route('MIRSSign')}}" method="post">
                {{ csrf_field() }}
                <button type="submit" id="accepted" name="MIRSNo" value="{{$MIRSMaster[0]->MIRSNo}}"><i class="fa fa-thumbs-up"></i>Approve</button>
              </form>
              <form class="Deny" action="denied" method="POST">
                {{ csrf_field() }}
                <input type="text" name="MIRSNo" value="{{$MIRSMaster[0]->MIRSNo}}" style="display:none">
                <button type="submit" id="not-accepted"><i class="fa fa-thumbs-down"></i>Disapprove</button>
              </form>
            </div>
          @endif
      @endif
    @elseif((Auth::user()->Role==4)&&$MCTNumber[0]==null)
      <a href="#"><button type="button" name="button"> <i class="fa fa-plus"></i> Make MCT</button></a>
    @elseif((Auth::user()->Role!=4))
      <h1>No MCT generated yet</h1>
    @endif
      </div>
      <div class="bondpaper-size">
        @if (isset($MIRSMaster[0]->MIRSNo) && isset($MIRSDetail[0]->MIRSNo))
        <div class="top-part-box1">
          <h1>BOHOL 1 ELECTRIC COOPERATIVE, INC.</h1>
            <h4>Cabulijan, Tubigon, Bohol</h4>
            <h2>MATERIALS ISSUANCE REQUISITION SLIP</h2>
            <div class="status-mirs">
              @if (($MIRSMaster[0]->PreparedSignature==null)||($MIRSMaster[0]->ApproveSignature==null)||($MIRSMaster[0]->RecommendSignature==null))
              <h1><i class="fa fa-clock-o"></i><br>Pending</h1>
              @else
              <h1 class="approved-sign"><i class="fa fa-thumbs-up"></i> <br>Approved</h1>
              @endif
            </div>
        </div>
        <div class="top-part-box2">
          <div class="top-box2-left">
          </div>
          <div class="top-box2-right">
            <div class="top-box2-right-data">
              <label>MIRS #:</label><h1>{{$MIRSMaster[0]->MIRSNo}}</h1>
            </div>
            <div class="top-box2-right-data">
              <label>Date:</label><h1>{{$MIRSMaster[0]->MIRSDate->format('M d, Y')}}</h1>
            </div>
          </div>
        </div>
        <div class="top-part-box3">
          <p>
            TO: The General Manager <br>
            Please furnish the following materials for :
          </p>
          <div class="purpose-container">
            <h2>{{$MIRSMaster[0]->Purpose}}</h2>
          </div>
        </div>
        <div class="mirs-details-container">
          <div class="table-mirs-container">
            <table>
              <tr>
                <th class="noborder-left">CODE</th>
                <th>PARTICULARS</th>
                <th>UNIT</th>
                <th>QUANTITY</th>
                <th class="noborder-right">REMARKS</th>
              </tr>
              @foreach ($MIRSDetail as $details)
              <tr>
                <td class="noborder-left">{{$details->ItemCode}}</td>
                <td class="particular">{{$details->Particulars}}</td>
                <td>{{$details->Unit}}</td>
                <td>{{$details->Quantity}}</td>
                <td>{{$details->Remarks}}</td>
              </tr>
              @endforeach
            </table>
          </div>
          <div class="statement-container">
            <p>I hereby certify that the materials/supplies requested above are <br>necessary and with purpose stated above</p>
          </div>
          <div class="bottom-mirs-part">
            <div class="request-recommend-sig">
              <div class="request-sig">
                <h4>Prepared by:</h4>
                <h3>
                  @if (!empty($MIRSMaster[0]->PreparedSignature))
                    <img src="/storage/signatures/{{$MIRSMaster[0]->PreparedSignature}}">
                  @endif
                </h3>
                <h2>
                  {{$MIRSMaster[0]->Preparedby}} <br>
                  {{$MIRSMaster[0]->PreparedPosition}}
                </h2>
              </div>
              <div class="recommend-sig">
              <h4>Recommended by:</h4>
              <h3>
                @if (!empty($MIRSMaster[0]->RecommendSignature))
                  <img src="/storage/signatures/{{$MIRSMaster[0]->RecommendSignature}}">
                @endif
              </h3>
              <h2>
              <span class="bold">{{$MIRSMaster[0]->Recommendedby}}</span><br>
                {{$MIRSMaster[0]->RecommendPosition}}
              </h2>
              </div>
            </div>
            <div class="president-sig">
              <h4>APPROVED:</h4>
              <h3>
                @if (!empty($MIRSMaster[0]->ApproveSignature))
                  <img src="/storage/signatures/{{$MIRSMaster[0]->ApproveSignature}}">
                @endif
              </h3>
              <h2>
              <span class="bold">{{$MIRSMaster[0]->Approvedby}}</span><br>
                {{$MIRSMaster[0]->ApprovePosition}}
              </h2>
            </div>
          </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

@endsection
