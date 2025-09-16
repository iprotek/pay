
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
                    <p class="login-box-msg">Sign in to start your session</p>
                        <div class="col-12">
                            <x-input.checkbox x-label-trans-id="secured_login" x-text="SECURED LOGIN" x-id="is_secured" onchange="secured_passchanged(event)"/>
                        </div>
                        <x-input.textbox id="username" x-icon="fa-id-card" x-type="email" x-text="COMPANY ID / EMAIL" onkeyup="userInputField(event)"/>
                        <div id="passcontainer">
                            <x-input.textbox id="userpass" x-icon="fa-key" x-type="password" x-text="Password" onkeyup="userInputField(event)"/>
                        </div>
                        <!--<x-display.progressbar x-text="Sign In" x-type="primary" x-kind="progressbar2" x-value="50" x-icon="fa-key" />
                        -->
                        <div class="row">
                        <div class="col-6" >
                            <select class="form-control languagelist">
                            </select>
                            <div id="remembercontainer">
                                <x-input.checkbox x-label-trans-id="remember_me" x-text="Remember Me" x-id="remember"/>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-6">
                            <!--<button type="submit" class="btn btn-primary btn-block">Sign In</button>
                            
                            <x-input.button text="Sign In" class="omg"/>-->
                            
                            <x-input.button class="admin-login-input" x-label-trans-id="sign_in" style="float:right;" x-text="Sign In" x-type="primary" x-kind="button4" x-icon="fa-lock" data-source="LoginInfo" data-source-dynamic="1" sys-submit="XposeSignIn" sys-submit-method="POST" sys-submit-url="login" sys-send-text="Signing..." sys-result-text="Succeed"/>
                            <script>
                                function secured_passchanged(evt){
                                    //console.log(evt.target.checked);
                                    var is_checked =  document.querySelector('#is_secured').checked;
                                    //if(is_checked)
                                    var user_pass = document.querySelector('#passcontainer');
                                    user_pass.querySelector('#userpass').value = '';
                                    user_pass.style.display = is_checked ? '':'none';

                                    var remembercontainer = document.querySelector('#remembercontainer');
                                    remembercontainer.querySelector('#remember').checked = false;
                                    remembercontainer.style.display = is_checked ? '':'none';

                                }
                                secured_passchanged(null);

                                function userInputField(evt)
                                {
                                    if(evt.keyCode == 13)
                                    {
                                        document.querySelector('.admin-login-input').click();
                                    }
                                }
                                function XposeSignIn(evt, toSend, isSend, result)
                                {
                                    if(!isSend)
                                    {
                                        toSend.data = {
                                            "email" : document.querySelector('#username').value,
                                            "password" : document.querySelector('#userpass').value, 
                                            "noreload" : 1, 
                                            "remember" : (document.querySelector('#remember').checked ? 1 : 0)
                                        }
                                        //toSend.allowSubmit = false;
                                        if(!toSend.data.email)
                                        {
                                            console.log("Show failed login");
                                            toSend.allowSubmit = false;
                                        }
                                    }
                                    else
                                    {
                                        if(result.RetVal == 1)
                                            location.reload();
                                            console.log(result);
                                    }
                                }
                            </script>
                        </div>
                        <!-- /.col -->
                        </div>
                    <!--
                    <div class="social-auth-links text-center mt-2 mb-3">
                        <a href="#" class="btn btn-block btn-primary">
                        <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
                        </a>
                        <a href="#" class="btn btn-block btn-danger">
                        <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
                        </a>
                    </div> -->
                    <!-- /.social-auth-links -->
                    
                    <hr/>
                        <a href="/create-suggestion" ><h6 class="text-center text-red"> <i class="btn btn-default"> <b class="text-red">- - CREATE SUGGESTION NOW - -</b> </i> </h6> </a>
                    <hr/>
                    <?php if($hasForgetPass){ ?>
                        <p class="mb-1">
                            <a href="/recovery-request"> <label style="cursor:pointer;" class="m-0 text-info" label-trans-id="account_recovery_request"> Account Recovery Request</label></a>
                        </p>
                    <?php } ?>

                    <?php if($hasRegister){ ?>
                        <p class="mb-0">
                            <a href="/register" class="text-center" ><label style="cursor:pointer;" class="m-0 text-info" label-trans-id="register">Register</label></a>
                        </p>
                    <?php } ?>
                </x-container.card>
            
            </div>
        </div>
    </div>
    <!-- /.login-box -->
@endsection

@include('iprotek_pay::layout.pages.view-page')
