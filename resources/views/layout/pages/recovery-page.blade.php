
@section('content')
    <div class="login-box" style="width:70%; max-width:700px;">
    <!-- /.login-logo -->
        <div  style="height:100%;;min-height:530px;min-width:650px;">
            <!--
            <div style="display:flex; flex-wrap:wrap; align-content:center; width:50%; height:100%; float:left; background-color:#a80c0c; align-items:center;">
                <h1 class="text-white w-100 " style="margin-left:25%; margin-bottom:0px; font-size:75px; line-height:60px;"> <i><b>SCI</b></i> </h1>
                <label class="text-white w-100" style="margin-left:25%; margin-bottom:0px;">Partner</label>
                <label class="text-white w-100" style="margin-left:25%; margin-bottom:0px;">for Success</label>
            </div>
            -->
            <div style="width:50%; height:100%; float:left; background-color:#ffdfdf;border-radius:7px; border:3px solid #dfc3c3;">
                <div style="float:left;">
                    <h1 class="w-100" style="color:#a80c0c; margin-left:5%; margin-top:10px;  margin-bottom:0px; font-size:47px; line-height:20px;"> <i><b>SCI</b></i> </h1>
                    <label class="w-100" style="color:#a80c0c; margin-top:-3%; margin-left:5%; margin-bottom:0px; font-size:8px;">Partner for Success</label>
                </div>
                <div style="display:flex; flex-wrap:wrap; align-content:center; width:50%;  ">
                    <img src="/images/kaizen-logo.png" style="width:100%;"/>
                </div>
                <div>
                    <h4  style="text-align:center; font-size:35px;"><b style="color: #e69502; text-shadow:0px 0px 0px #6c6a6a, -3px -2px 0px #951717; font-family:Arial;">DIGITAL KAIZEN</b></h4>
                </div>
                <div style="background-color:#a80c0c; border-radius:10px;margin: 0px 20px; padding: 5px 10px; color:white; font-family:Arial;">
                    <div style=" padding-bottom:3px; border-bottom: 2px solid white;">
                        <span class="fa fa-arrow-down" style="border:2px solid white; padding:2px 2px 1px 2px; border-radius:50%;"></span> <label class="m-0" style="font-weight: initial;">ABOUT US</label>
                    </div>
                    <h5 class="m-0"><b>VISION<b></h5>
                    <ul class="mb-1">
                        <li class="p-0 m-0" style="font-weight: initial;"> Partner for Success</li>
                    </ul>
                    <h5 class="m-0"><b>MISSION<b></h5>
                    <ul class="mb-1">
                        <li class="p-0 m-0" style="font-weight: initial;"> Product and Service Provider</li>
                    </ul>
                    <h5 class="m-0"><b>CORE VALUES<b></h5>
                    <ul class="mb-1">
                        <li class="p-0 m-0" style="font-weight: initial;"> Teamwork</li>
                        <li class="p-0 m-0" style="font-weight: initial;"> Initiative</li>
                        <li class="p-0 m-0" style="font-weight: initial;"> Accountability</li>
                        <li class="p-0 m-0" style="font-weight: initial;"> Continuous Improvement</li>
                    </ul>
                </div>
            </div>
            <div style="display:inline; width:50%; height:100%; float:left;">
                
                <?php 
                    $title = view()->getSection('login-title', 'N/A');
                    $type = view()->getSection('login-type', 'primary');
                ?>
                <x-container.card :x-type="$type " :x-title="$title" class="h2 text-red text-italic" style=";min-height:530px;" >
                    
                        <div class="col-sm-12" id="recovery-form">
                            <p class="login-box-msg">Please fill in.</p>
                            <x-input.textbox id="recovery-username" x-icon="fa-id-card" x-type="text" x-text="COMPANY ID"  />
                            <!-- <x-input.checkbox x-label-trans-id="secured_login" x-text="SECURED LOGIN" x-id="is_secured" onchange="secured_passchanged(event)"/>-->
                            <x-input.textbox id="recovery-first-name" x-icon="fa-id-card" x-type="text" x-text="FIRST NAME"  />
                            <x-input.textbox id="recovery-last-name" x-icon="fa-id-card" x-type="text" x-text="LAST NAME" />
                            <x-input.textbox id="recovery-email" x-icon="fa-envelope" x-type="email" x-text="EMAIL" />
                            REASON:
                            <textarea style="width:100%;" id="recovery-reason"></textarea>
                            <x-input.button class="admin-login-input" onclick="sendingRequest()"  x-text="Send Request" x-type="primary" x-kind="button4" x-icon="fa-restore" />
                        </div>
                        <div class="col-sm-12" id="recovery-sending-request" style="display:none;">
                            <p class="login-box-msg">
                                <span class="fa fa-pulse fa-redo"></span>
                                Sending Request.<br/>
                                Please wait..
                            </p>
                        </div>
                        <div class="col-sm-12" id="recovery-inform" style="display:none;">
                            <label id="recovery-inform-message">
                                Your request has been received. We will inform you once approved.
                            </label><br/><br/><br/><br/>
                            <label style="cursor:pointer;" onclick="resendingNewRequest()" class="m-0 text-primary" >Resend new recovery request.</label>
                        </div>
                        
                        <br/>
                        <p class="mb-0">
                            <a href="/login" class="text-center" ><label style="cursor:pointer;" class="m-0 text-info" label-trans-id="login">Login</label></a>
                        </p>
                        <script>
                            function sendingRequest(){
                                document.querySelector('#recovery-form').style.display = 'none';
                                document.querySelector('#recovery-inform').style.display = 'none';
                                document.querySelector('#recovery-sending-request').style.display = '';

                                var req = {
                                    company_id : document.querySelector('#recovery-username').value,
                                    first_name : document.querySelector('#recovery-first-name').value,
                                    last_name : document.querySelector('#recovery-last-name').value,
                                    email : document.querySelector('#recovery-email').value,
                                    reason: document.querySelector('#recovery-reason').value
                                };
                                WebRequest2('POST','/recovery-requisition', JSON.stringify(req), 'application/json').then(resp=>{
                                    
                                    document.querySelector('#recovery-form').style.display = 'none';
                                    document.querySelector('#recovery-sending-request').style.display = 'none';
                                    document.querySelector('#recovery-inform').style.display = '';
                                    if(resp.ok){
                                        document.querySelector('#recovery-inform-message').innerHTML = "Your request has been received. We will inform you once approved.";
                                    }
                                    else{
                                        resp.json().then(data=>{
                                            document.querySelector('#recovery-inform-message').innerHTML = data.message;
                                        })
                                    }
                                });
                            }
                            function resendingNewRequest(){
                                document.querySelector('#recovery-form').style.display = '';
                                document.querySelector('#recovery-inform').style.display = 'none';
                                document.querySelector('#recovery-sending-request').style.display = 'none';

                                document.querySelector('#recovery-username').value = '';
                                document.querySelector('#recovery-first-name').value = '';
                                document.querySelector('#recovery-last-name').value = '';
                                document.querySelector('#recovery-email').value = '';
                                document.querySelector('#recovery-reason').value = '';

                            }

                        </script>
                        
                      
                </x-container.card>
            
            </div>
        </div>
    </div>
    <!-- /.login-box -->
@endsection

@include('iprotek_pay::layout.pages.view-page')
