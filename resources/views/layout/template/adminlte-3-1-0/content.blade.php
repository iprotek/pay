<div class="wrapper">
    

<!-- Navbar -->
    @include("iprotek_pay::layout.template.adminlte-3-1-0.navigation")

    <!-- Main Sidebar Container -->
    @include("iprotek_pay::layout.template.adminlte-3-1-0.sidebar")

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="min-height: 1172.8px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>@yield('site-title','Sample')</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              @yield('breadcrumb', 'sample')
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="modal fade bd-example-modal-lg" data-backdrop="static" data-keyboard="false" tabindex="-1" style="z-index:99999;height:">
          <div class="modal-dialog modal-sm">
              <div style="width: 48px;">
                  <span class="fa fa-spinner fa-spin fa-3x text-white"></span>
              </div>
          </div>
      </div>
        @yield("content")  
        <!-- /.container-fluid -->

    </section>
    <!-- /.content -->

    <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
      <i class="fas fa-chevron-up"></i>
    </a>
  </div>
  <!-- /.content-wrapper -->

  @include("iprotek_pay::layout.template.adminlte-3-1-0.content-footer")

</div>