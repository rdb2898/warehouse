@extends('layouts.master')
@section('title')
  RR of this RV | history
@endsection
@section('body')
<div class="RRlistofRV-container">
<h1 class="title-rr-of-rv">All R.R. of R.V. Number <span class="color-blue">{{$RRofRV[0]->RVNo}}</span></h1>
  <div class="rr-of-rv-box-container">
    @foreach ($RRofRV as $rr)
      <a href="{{route('RRfullpreview',[$rr->RRNo])}}">
        <div class="box-rr-of-rv">
          <div class="rr-num-top">
            <h1 class="rollback-sign">
              @if ($rr->IsRollBack=='0')
                <p></p>
              @endif
              RR : {{$rr->RRNo}}
            </h1>
            <h2>{{$rr->Supplier}} ({{$rr->Address}})</h2>
            <h3>{{$rr->RRDate->diffForHumans()}}</h3>
            <div class="triangle-top-right">
            </div>
          </div>
          @if ($rr->Status=='0')
            <h1><i class="material-icons">thumb_up</i></h1>
          @elseif ($rr->Status=='1')
            <h3><i class="material-icons big-x">close</i></h3>
          @else
            <h2><i class="material-icons big-clock">access_time</i></h2>
          @endif
        </div>
      </a>
    @endforeach
  </div>
  <div class="paginate-container">
    {{$RRofRV->links()}}
  </div>
</div>
@endsection
