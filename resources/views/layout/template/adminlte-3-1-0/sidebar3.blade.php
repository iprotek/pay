  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
    <div style="margin-left:15px; width:40px; overflow:hidden;float:left;">
    <!--<img src="/img/cartly.png" alt="ALTE" class="brand-image elevation-3" style="opacity: .8; margin-left:-5px;">-->
    </div>
      <span class="brand-text font-weight-light"><b>PRS</b></span> 
         
    </a>

    <?php
      if($USER != null)
      {
        $USERFULLNAME = $USER->name;
        $user_type = $USER->getCompanyUserTypeName();
        $company_name =  $USER->getCurrentCompanyName();
        //$EMPLOYEEPHOTO = "https://employeephoto.sportscity.com.ph/pams-photo/".$USER->company_id;
        $USERFULLNAME = $USER->name;
        $EMPLOYEEPHOTO = "/images/temp-image.png";
        //$EMPLOYEEPHOTO = $USER->default_image;
      }
      if(!isset($USER))
      {
        $USERFULLNAME = "";
        $user_type = "";
        $company_name =  $USER->getCurrentCompanyName();
        $EMPLOYEEPHOTO = "/iprotek/design/templates/adminlte3.1.0/dist/img/user2-160x160.jpg";
      }
    ?>
    <div>
      <small class="d-block text-white text-center mt-1">{{$company_name}}</small>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-1 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?=$EMPLOYEEPHOTO?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?=$USERFULLNAME?></a>
          @if($user_type)
            <small class="d-block text-white"><i>{{$user_type}}</i></small> 
          @endif
        </div>
      </div>
      <!-- SidebarSearch Form -->
      

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            @foreach($SIDEMENUS as $sidemenu)
                <?php if(!empty($sidemenu->items)){?> 
                <li class="nav-header" label-trans-id="group-menu-{{$sidemenu->id}}"><?=$sidemenu->group_text?></li>
                <?php $menuitems = $sidemenu->items;?>
                  @foreach($menuitems as $menuitem)
                    @if($menuitem->type == 'combo')
                    <?php
                      $menuitemcombo = $menuitem;?>
                      <x-container.sidemenucombobox :x-text="$menuitem->menu_text" :x-icon="$menuitem->icon" :badges="json_encode([])">
                          <?php $badges = [];
                            $comboboxitems = $menuitemcombo->items;?>
                          @foreach($comboboxitems as $comboboxitem)
                            <?php //var_dump($comboboxitems); ?>
                            <x-container.sidemenu :x-text="$comboboxitem->menu_text" :x-icon="$comboboxitem->icon" :x-url="$comboboxitem->url" :badges="json_encode([])" x-label-trans-id="sub-menu-{{$comboboxitem->id}}"/>
                          @endforeach
                      </x-container.sidemenucombobox>
                    @elseif($menuitem->type == 'menu')
                      <?php //var_dump($menuitem); 
                        //$user_types = explode(',', $menuitem->user_types);
                        if( $USER->can('menu-name-check', $menuitem->menu_text) ){
                      ?>
                          <x-container.sidemenu :x-text="$menuitem->menu_text" :x-icon="$menuitem->icon" :x-url="$menuitem->url" :badges="json_encode([])" x-label-trans-id="sub-menu-{{$menuitem->id}}-{{$menuitem->menu_text}}"/>
                      <?php } ?>
                      @endif
                  @endforeach
                <?php }?>
            @endforeach 
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
