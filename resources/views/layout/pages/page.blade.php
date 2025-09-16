
@section('head')

  <!-- Font Awesome -->
  <link rel="stylesheet" href="/iprotek/design/templates/adminlte3.1.0/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="/iprotek/design/templates/adminlte3.1.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/iprotek/design/templates/adminlte3.1.0/dist/css/adminlte.min.css">

@endsection

@section('foot')

    <script src="/iprotek/design/templates/adminlte3.1.0/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/iprotek/design/templates/adminlte3.1.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/iprotek/design/templates/adminlte3.1.0/dist/js/adminlte.min.js"></script>

@endsection
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include("iprotek_pay::layout.template.adminlte-3-1-0.head")
    </head>
    <body >
        <!-- /*class="hold-transition login-page"*/ -->
        <div >
            <div id="main-container" style="width:100%;">
                @yield("content")
            </div>
        </div>
    </body>
    <footer>
        @include("iprotek_pay::layout.template.adminlte-3-1-0.footer")
    </footer>
</html>

