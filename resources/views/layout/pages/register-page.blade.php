
@section('content')
    <div class="login-box">
    <!-- /.login-logo -->
    <?php 
        $title = view()->getSection('register-title', 'N/A');
        $type = view()->getSection('register-type', 'primary');
    ?>
    
    <div class="modal fade bd-example-modal-lg" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div style="width: 48px;">
                <span class="fa fa-spinner fa-spin fa-3x text-white"></span>
            </div>
        </div>
    </div>
    <datalist id="region_names">
    <option value="PH1">
    <option value="PH2">
    </datalist>
    <x-container.card :x-type="$type " :x-title="$title" class="h2" >
        <p class="login-box-msg" > <label class="m-0" label-trans-id="register_as_a_new_member"> Register as a new member </label> </p>
            <x-input.textbox id="username" onkeyup="requireKeyUp(event)" x-icon="fa-id-card" class="text-uppercase border border-danger" x-type="text" x-text="ID NUMBER" required/>
            <x-input.textbox id="first_name" onkeyup="requireKeyUp(event)"  x-icon="fa-language" class="text-uppercase border border-danger" x-type="text" x-text="First Name" required/>
            <x-input.textbox id="last_name" onkeyup="requireKeyUp(event)"  x-icon="fa-language" class="text-uppercase border border-danger" x-type="text" x-text="Last Name" required/>
            <div class="input-group mb-3">
                <select onchange="requireKeyUp(event)" class="m-0 form-control rounded-0 input-sm regionlist border border-danger" id="region_id" required>
                    <option value="" style="text-transform: uppercase;"> -- REGION -- </option> 
                </select>
                <span class="input-group-text rounded-0 border-0"><i class="fa fa-building"></i></span> 
            </div>
            <div class="input-group mb-3">
                <select onchange="requireKeyUp(event)" class="m-0 form-control rounded-0 input-sm factorylist border border-danger" id="factory_id" required>
                    <option value="" style="text-transform: uppercase;"> -- FACTORY -- </option> 
                </select>
                <span class="input-group-text rounded-0 border-0"><i class="fa fa-building"></i></span> 
            </div>
            <div class="input-group mb-3">
                <select class="m-0 form-control rounded-0 input-sm departmentlist border border-danger" id="department_id" onchange="DepartmentChanged(event)" required>
                    <option value="" style="text-transform: uppercase;"> -- DEPARTMENT -- </option> 
                </select>
                <span class="input-group-text rounded-0 border-0"><i class="fa fa-globe"></i></span> 
            </div>
            <!--<x-input.textbox onchange="requireKeyUp(event)" id="line" x-icon="fa-language" class="text-uppercase border border-danger" x-type="text" x-text="Section/Line" required/>-->
            <div class="input-group mb-3">
                <select onchange="requireKeyUp(event)" class="m-0 form-control rounded-0 input-sm border border-danger select2" id="line" required>
                    <option value="" style="text-transform: uppercase;"> -- SECTION / LINE -- </option> 
                </select>
                <span class="input-group-text rounded-0 border-0"><i class="fas fa-language"></i></span> 
            </div>
            <div class="input-group mb-3">
                <select onchange="requireKeyUp(event)" class="m-0 form-control rounded-0 input-sm border border-danger" id="position_id" required>
                    <option value="" style="text-transform: uppercase;"> -- POSITION -- </option> 
                </select>
                <span class="input-group-text rounded-0 border-0"><i class="fa fa-user-tag"></i></span> 
            </div>

            <x-input.textbox id="email" x-icon="fa-envelope" x-type="email" x-text="Email (Optional)"/>
            <div class="input-group mb-3">
                <select onchange="requireKeyUp(event)" class="m-0 form-control rounded-0 input-sm border border-danger" id="status_id" required>
                    <option value="" style="text-transform: uppercase;"> -- STATUS -- </option> 
                    <option value="1" style="text-transform: uppercase;"> REGULAR </option> 
                    <option value="2" style="text-transform: uppercase;"> PROBATIONARY </option> 
                </select>
                <span class="input-group-text rounded-0 border-0"><i class="fa fa-user-tag"></i></span> 
            </div>
            <div class="row">
            <div class="col-6">
                <x-input.checkbox x-text="I agree the <a href='#'>terms</a>" x-id="agree"/>
            </div>
            <!-- /.col -->
            <div class="col-6">
                
                <x-input.button style="float:right;" x-label-trans-id="register" x-text="Register" x-type="primary" x-kind="button4" x-icon="" data-source="LoginInfo" data-source-dynamic="1" sys-submit="XposeRegister" sys-submit-method="POST" sys-submit-url="/register" sys-send-text="Registering.." sys-result-text="Succeed"/>
                
                
                <script>
                    function requireKeyUp(evt){
                        if(evt.target.value.trim() == ""){
                            evt.target.classList.add('border');
                            evt.target.classList.add('border-danger');
                        }else{
                            evt.target.classList.remove('border');
                            evt.target.classList.remove('border-danger');
                        }
                        if(evt.target.id == "department_id"){

                            
                            var line = document.querySelector('#line');
                            line.value = '';
                            line.classList.add('border');
                            line.classList.add('border-danger');

                            var position_id = document.querySelector('#position_id');
                            position_id.value = '';
                            position_id.classList.add('border');
                            position_id.classList.add('border-danger');
                            document.querySelector('#line').value = '';
                            
                            position_id.querySelectorAll('OPTION').forEach((positionItem, positionIndex)=>{
                                if(positionItem.value == '') return;
                                if(evt.target.value == '')
                                    positionItem.style.display = '';                                
                                positionItem.style.display = positionItem.getAttribute('data-department-id') == evt.target.value ? '':'none';

                            });


                        }
                        else if(evt.target.id == "factory_id"){

                            var line = document.querySelector('#line');
                            line.value = '';
                            line.classList.add('border');
                            line.classList.add('border-danger');
                            
                            //Regions
                            var region_id =  document.querySelector('#region_id');
                            var fac_data = evt.target.options[evt.target.selectedIndex].Data;
                            if(fac_data){
                               region_id.classList.remove('border');
                               region_id.classList.remove('border-danger');
                               region_id.value = fac_data.region_id;
                            }
                            else{
                               region_id.classList.add('border');
                               region_id.classList.add('border-danger');
                               region_id.value = '';
                            }
                            

                            //Departments
                            var department_id = document.querySelector('#department_id');
                            department_id.value = '';
                            department_id.classList.add('border');
                            department_id.classList.add('border-danger');
                            department_id.querySelectorAll('OPTION').forEach((depItem,depIndex)=>{
                                if(depItem.value == '') return;
                                if(evt.target.value == '') depItem.style.display = 'none';
                                var res = JSON.parse( depItem.Data.regions).filter((a)=>(a == fac_data.region_id));
                                depItem.style.display = res[0] ? '':'none';
                            });


                        }
                        else if(evt.target.id == "region_id"){
                            var line = document.querySelector('#line');
                            line.value = '';
                            line.classList.add('border');
                            line.classList.add('border-danger');

                            var position_id = document.querySelector('#position_id');
                            position_id.value = '';
                            position_id.classList.add('border');
                            position_id.classList.add('border-danger');
                            var factory_id = document.querySelector('#factory_id');
                            factory_id.value = '';
                            factory_id.classList.add('border');
                            factory_id.classList.add('border-danger');
                            factory_id.querySelectorAll('OPTION').forEach((factoryItem, factoryIndex)=>{
                                //console.log(factoryItem.Data);
                                if(factoryItem.value == '') return;
                                if(evt.target.value == '')
                                {
                                    factoryItem.style.display = '';
                                    return;
                                }                                
                                factoryItem.style.display = factoryItem.Data.region_id == evt.target.value ? '':'none';
                            });
                            
                            //Departments
                            var department_id = document.querySelector('#department_id');
                            department_id.value = '';
                            department_id.classList.add('border');
                            department_id.classList.add('border-danger');
                            department_id.querySelectorAll('OPTION').forEach((depItem,depIndex)=>{
                                //console.log(depItem.Data);
                                if(depItem.value == '') return;
                                if(evt.target.value == '') depItem.style.display = 'none';
                                var res = JSON.parse( depItem.Data.regions).filter((a)=>(a == evt.target.value));
                                //console.log(res);
                                depItem.style.display = res[0] ? '':'none';
                                

                            });

                        }
                    }


                    function DepartmentChanged(evt){
                        //console.log("HELLO");
                        requireKeyUp(evt);
                        var position_id = document.querySelector('#position_id');
                        var department_id = document.querySelector('#department_id');
                        var line = document.querySelector('#line');
                        //console.log(position_id);
                        position_id.value = '';
                        line.value = '';
                        position_id.querySelectorAll('option').forEach((positionItem, positionIndex)=>{
                            if(positionItem.value != '')
                                positionItem.remove();
                        });
                        line.querySelectorAll('option').forEach((lineItem, lineIndex)=>{
                            if(lineItem.value != '')
                                lineItem.remove();
                        });
                        console.log(department_id, department_id.vlaue);
                        /*
                        for(var i = 0; i < position_id.options.length; i++){
                            var _option = position_id.options[i];
                            if(_option.Data){
                                if(department_id.value == '')
                                {
                                    _option.style.display = '';
                                }
                                else if(department_id.value == _option.Data.department_id){
                                    _option.style.display = '';
                                }
                                else  _option.style.display = 'none';
                            }


                        }*/
                        //console.log(department_id, department_id.vlaue);
                        if(department_id.value == "")
                            return;
                            
                        //SECTION
                        WebRequest('GET','/section-list/'+department_id.value, null, 'application/json', result=>{ 
                            result.forEach((sectionItem, sectionIndex)=>{
                                var option = document.createElement('OPTION');
                                option.value = sectionItem.name;
                                option.innerHTML = sectionItem.name;
                                line.append(option);
                            });
                        });
                        //POSITION
                        WebRequest('GET', '/position-list/'+ department_id.value, null, 'application/json', result=>{ 
                            //vm.position_list = result; 
                            result.forEach((posItem, posIndex)=>{
                                var option = document.createElement('OPTION');
                                option.value = posItem.id;
                                option.innerHTML = posItem.name;
                                position_id.append(option);
                            })
                        });

                    }


                    function XposeRegister(evt, toSend, isSend, result)
                    {
                        if(!isSend)
                        {
                            var hasError = false;
                            var errorLog = document.querySelector('#error-log');
                            var successLog = document.querySelector('#success-log');

                            errorLog.innerHTML = "";
                            successLog.innerHTML = "";
                            var _box = document.querySelector('.login-box');

                            toSend.data = {
                                "username" : _box.querySelector('#username').value.toUpperCase(),
                                "first_name" : _box.querySelector('#first_name').value.toUpperCase(),
                                "last_name" : _box.querySelector('#last_name').value.toUpperCase(),
                                "factory_id" : _box.querySelector('#factory_id').value,
                                "department_id" : _box.querySelector('#department_id').value,
                                "line" : _box.querySelector('#line').value.toUpperCase(),
                                "position_id" : _box.querySelector('#position_id').value,
                                "email" : _box.querySelector('#email').value,
                                "status_id" : _box.querySelector('#status_id').value
                            }
                            if(!toSend.data.username || !toSend.data.first_name || 
                                !toSend.data.last_name || !toSend.data.factory_id|| 
                                !toSend.data.department_id || !toSend.data.line || 
                                !toSend.data.position_id || !toSend.data.status_id
                                )
                            {
                                errorLog.innerHTML = "Please fill up all required data";
                                hasError = true;
                            }
                            else if(!document.querySelector('#agree').checked)
                            {
                                errorLog.innerHTML = "You are required to agree before you can register";
                                hasError = true;
                            }
                            /*
                            if(document.querySelector('#userpass').value != document.querySelector('#vuserpass').value)
                            {
                                errorLog.innerHTML = "Password doesn't match!";
                                hasError = true;
                            }
                            */
                            //toSend.allowSubmit = false;
                            //console.log(toSend.data);
                            if(hasError)
                            {
                                toSend.allowSubmit = false;
                            }
                        }
                        else
                        {
                            var errorLog = document.querySelector('#error-log');
                            var successLog = document.querySelector('#success-log');
                            errorLog.innerHTML = "";
                            successLog.innerHTML = "";
                            if(result.RetVal == 1)
                            {
                                
                                successLog.innerHTML = "You have successfully registered.<br/>You are now automatically login.<br/> No password required.";
                                document.querySelectorAll('input, select').forEach((el, index)=>{
                                    
                                    if(el.required){
                                        el.classList.add('border');
                                        el.classList.add('border-danger');
                                    }
                                    
                                    if(el.type == 'checkbox')
                                        el.checked = false;
                                    else{
                                        el.value = '';
                                    }
                                    
                                });
                                if(result.DataID == 1){
                                    setTimeout(() => {
                                        window.location = "/admin";//.reload();
                                    }, 4000);
                                }
                                
                            }
                            else if(result.DataID = -1)
                            {
                                errorLog.innerHTML = result.Message;
                            }
                        }
                    }
                </script>
            </div>
            <label id="error-log" class="text-red"></label>
            <label id="success-log" class="text-green"></label>
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
        @if($hasForgetPass)
            <p class="mb-1">
                <a href="forgot-password.html" label-trans-id="i_forgot_my_password">I forgot my password</a>
            </p>
        @endif

        @if($hasLogin)
            <p class="mb-0">
                <a href="/login" class="text-center" label-trans-id="login">Login</a>
            </p>
        @endif
    </x-container.card>
    </div>
    <!-- /.login-box -->
@endsection

@include('iprotek_pay::layout.pages.view-page')
