@extends('iprotek_core::layout.pages.view-dashboard')

@section('logout-link','/logout')
@section('site-title', 'DB Management')
@section('head')
     
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection
@section('breadcrumb')
    <!--
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Widgets</li>
    -->
@endsection
@section('content') 
  <div id="main-content">
        <dbm-view :group_id="{{$group_id}}" :branch_id="{{$selected_branch_id}}" />
  </div>
   
@endsection

@section('foot') 
  <script>
    //ActivateMenu(['menu-dashboard']);
  </script> 
  
    <script src="/iprotek/js/manage/system/dbm.js?v=1"> </script>
  
  
@endsection

