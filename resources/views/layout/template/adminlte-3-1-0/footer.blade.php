
<!-- jQuery -->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/moment/moment.min.js"></script>
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="/iprotek/design/templates/adminlte3.1.0/dist/js/adminlte.js"></script>
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/sweetalert2/sweetalert2.min.js"></script>

<script src="/iprotek/design/templates/adminlte3.1.0/plugins/select2/js/select2.min.js"></script>
<!-- AdminLTE for demo purposes 
<script src="/iprotek/design/templates/adminlte3.1.0/dist/js/demo.js"></script>-->
<!-- AdminLTE dashboard demo (This is only for demo purposes) 
<script src="/iprotek/design/templates/adminlte3.1.0/dist/js/pages/dashboard.js"></script>-->
<script src="/iprotek/design/templates/adminlte3.1.0/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script>
  
  const SwalToast = Swal.mixin({
                        toast: true,
                        position: 'bottom-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                  });

  function SwalPrompt( _title, _text, _icon, _confirmcolor = '#3085d6',   fn)
  {
    return Swal.fire({
            title: _title,
            text: _text,
            icon: _icon,
            showCancelButton: true,
            confirmButtonColor: _confirmcolor,
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
          }).then((result) => {
          if (result.isConfirmed) {
            if(fn!=null)
            {
              fn();
            }
          }
        });
  }
</script>


<script> 

<?php
    $user_id = 0;
    $user = auth('admin')->user();
    if($user != null)
      $user_id = $user->id;
?>
@if($user_id != 0) 
  function createBadge( menu_name , cl, val){
    var menu = document.querySelector('i.'+menu_name);
    if(menu){
       menu = menu.closest('a');
      if(menu && (val * 1)  ){
        var badge = document.createElement('span');
        badge.classList.add('right');
        badge.classList.add('badge');
        badge.classList.add(cl);
        badge.innerHTML = val;
        menu.append(badge);
      }
    }
  }

@endif


window.is_shown = false;
window.count_hiding = 0;
window.fnLoading = function(){
  if(window.is_shown)
  {
    $('.bd-example-modal-lg').modal('hide');
    if(window.count_hiding > 5)
    {
      clearInterval(window.fnLoadingInterval);
    }
    //console.log(window.is_shown);
    window.count_hiding++;
  }
}
window.fnLoadingInterval = null;
$(window).ready(function(){
    $('.bd-example-modal-lg').modal('show');
    window.fnLoadingInterval = setInterval(window.fnLoading, 500);
});

//$('.bd-example-modal-lg').modal('show');
WebRequest("GET", '/v2/Data/List', null, 'application/json', function(result){
  window.is_shown = true;
  $('.bd-example-modal-lg').modal('hide');
      //console.log(result);
      if(result.RetVal == 1)
      {
          window.List = result.Data;
      }
      //factorylist Element
      document.querySelectorAll('.factorylist').forEach((factoryEl, factoryElIndex)=>{
          //Fill element with factory
          if(factoryEl.tagName == "SELECT"){
              result.Data.FactoryList.forEach((factoryItem, factoryIndex)=>{
                  var option = document.createElement('OPTION');
                  option.Data = factoryItem;
                  option.innerHTML = factoryItem.name;
                  if(factoryEl.hasAttribute('value-name'))
                    option.value = factoryItem.name;
                  else
                    option.value = factoryItem.id;
                  option.setAttribute('data-id', factoryItem.id);
                  option.setAttribute('data-name', factoryItem.name);
                  factoryEl.append(option);
              });
          }
      });
      document.querySelectorAll('.departmentlist').forEach((departmentEl, departmentElIndex)=>{
          //Fill element with factory
          if(departmentEl.tagName == "SELECT"){
              result.Data.DepartmentList.forEach((depItem, departmentIndex)=>{
                  var option = document.createElement('OPTION');
                  option.Data = depItem;
                  option.innerHTML = depItem.name;
                  if(departmentEl.hasAttribute('value-name'))
                    option.value = depItem.name;
                  else
                    option.value = depItem.id;
                  option.setAttribute('data-id', depItem.id);
                  option.setAttribute('data-name', depItem.name);
                  departmentEl.append(option);
              });
          }
      });
      document.querySelectorAll('.linelist').forEach((lineEl, lineEIndex)=>{
          //Fill element with factory
          if(lineEl.tagName == "SELECT"){
              result.Data.LineList.forEach((lineItem, lineItemIndex)=>{
                  var option = document.createElement('OPTION');
                  option.Data = lineItem;
                  option.innerHTML = lineItem.line_section;
                  option.value = lineItem.line_section;
                  option.setAttribute('data-id', lineItem.line_section);
                  lineEl.append(option);
              });
          }
      });
      document.querySelectorAll('.regionlist').forEach((regionEl, regionEIndex)=>{
          //Fill element with factory
          if(regionEl.tagName == "SELECT"){
              result.Data.RegionList.forEach((regionItem, regionItemIndex)=>{
                  var option = document.createElement('OPTION');
                  option.Data = regionItem;
                  option.innerHTML = regionItem.name;
                  if(regionEl.hasAttribute('value-name'))
                    option.value = regionItem.name;
                  else
                    option.value = regionItem.id;
                  option.setAttribute('data-id', regionItem.id);
                  regionEl.append(option);
              });
          }
      });
      document.querySelectorAll('.classificationlist').forEach((classEl, classEIndex)=>{
          //Fill element with factory
          if(classEl.tagName == "SELECT"){
              result.Data.ClassificationList.forEach((clItem, clItemIndex)=>{
                  var option = document.createElement('OPTION');
                  option.Data = clItem;
                  option.innerHTML = clItem.name;
                  option.value = clItem.id;
                  option.setAttribute('data-id', clItem.id);
                  classEl.append(option);
              });
          }
      });
      document.querySelectorAll('.dep-pos-list').forEach((depPosEl, depPosElIndex)=>{
          //Fill element with factory
          if(depPosEl.tagName == "SELECT"){
              result.Data.PositionsDepartments.forEach((depPosItem, regionItemIndex)=>{
                  var option = document.createElement('OPTION');
                  option.Data = depPosItem;
                  option.innerHTML = depPosItem.position_name;
                  if(depPosEl.hasAttribute('value-name'))
                    option.value = depPosItem.position_name;
                  else
                    option.value = depPosItem.id;
                  option.setAttribute('data-id', depPosItem.id);
                  option.setAttribute('data-position-id', depPosItem.position_id);
                  option.setAttribute('data-department-id', depPosItem.department_id);
                  depPosEl.append(option);
              });
          }
      });


      document.querySelectorAll('select.biometrictypelist').forEach((biometrictypeSelect, biometrictypeElIndex)=>{

        result.Data.BiometricTypeList.forEach((biometricTypeItem, biometricTypeItemIndex)=>{
          var _option  = document.createElement('OPTION');
          _option.Data = biometricTypeItem;
          _option.value = biometricTypeItem.ID;
          _option.innerHTML = biometricTypeItem.Name;
          biometrictypeSelect.append(_option);

        });
      });
      
      document.querySelectorAll('select.biometrictypelist').forEach((biometrictypeSelect, biometrictypeElIndex)=>{

        result.Data.BiometricTypeList.forEach((biometricTypeItem, biometricTypeItemIndex)=>{
          var _option  = document.createElement('OPTION');
          _option.Data = biometricTypeItem;
          _option.value = biometricTypeItem.ID;
          _option.innerHTML = biometricTypeItem.Name;
          biometrictypeSelect.append(_option);

        });
      });

      
      document.querySelectorAll('select.biometricdevicelist').forEach((biometricSelect, biometricElIndex)=>{

        result.Data.BiometricDeviceList.forEach((biometricItem, biometricItemIndex)=>{
          var _option  = document.createElement('OPTION');
          _option.Data = biometricItem;
          _option.value = biometricItem.ID;
          _option.innerHTML = biometricItem.Name + ' - IP: '+ biometricItem.IPAddress+':'+biometricItem.Port +' Type: ( '+ biometricItem.BiometricTypeName + ' )';
          biometricSelect.append(_option);

        });
      });



    


  });

  <?php 
    if( isset($hasSearch) &&  $hasSearch == false) { ?>
    document.querySelector('.fas.fa-search').closest('li.nav-item').remove();
  <?php } ?>
  


  //document.querySelectorAll('select.factorylist').fore

</script>
<script src="/iprotek/js/xpose/Xpose-Translations.js?version=1"></script>
<script>
  //$('select.select2').val("");
  $('select.select2').select2();//.val("");
  $(document).on('select2:open', () => {
    document.querySelector('.select2-search__field').focus();
    //console.log("HELLO");
  });
 </script>


@yield('foot')