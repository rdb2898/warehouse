@extends('layouts.master')
@section('title')
  My RR Signature request
@endsection
@section('body')
  <div class="RRrequest-container">
    <div class="title-rr-request">
      <h1><i class="fa fa-pencil"></i> Signature request RR</h1>
    </div>
    <div class="requestRR-table">
      @if (!empty($requestRR[0]))
      <table>
        <tr>
          <th>RR No.</th>
          <th>RR Date</th>
          <th>Supplier</th>
          <th>Address</th>
          <th>RV No.</th>
          <th>View</th>
        </tr>
        @foreach ($requestRR as $requestSignature)
          <tr>
            <td>{{$requestSignature->RRNo}}</td>
            <td>{{$requestSignature->RRDate->format('M, d Y')}}</td>
            <td>{{$requestSignature->Supplier}}</td>
            <td>{{$requestSignature->Address}}</td>
            <td>{{$requestSignature->RVNo}}</td>
            <td><a href="{{route('RRfullpreview',[$requestSignature->RRNo])}}"><i class="fa fa-eye"></i></a></td>
          </tr>
        @endforeach
      </table>
      @else
        <h1 class="no-RR">RR request is empty</h1>
      @endif
      @if (!empty($requestRR[0]))
      <div class="paginate-container">
        {{$requestRR->links()}}
      </div>
      @endif
    </div>
  </div>
@endsection
