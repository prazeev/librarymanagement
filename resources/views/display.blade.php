@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <center>
                    <div class="alert alert-success" id="success" style="display: none">

                    </div>
                    <div class="alert alert-danger" id="error" style="display: none">

                    </div>
                    <div id="qr-reader" style="width:500px"></div>
                    <div id="qr-reader-results"></div>
                </center>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{url('js/qr.min.js')}}"></script>
    <script>
        var base_url = window.location.origin;
        function docReady(fn) {
            // see if DOM is already available
            if (document.readyState === "complete"
                || document.readyState === "interactive") {
                // call on next available tick
                setTimeout(fn, 1);
            } else {
                document.addEventListener("DOMContentLoaded", fn);
            }
        }

        docReady(function () {
            var resultContainer = document.getElementById('qr-reader-results');
            var lastResult, countResults = 0;
            function onScanSuccess(qrCodeMessage) {
                if (qrCodeMessage !== lastResult) {
                    ++countResults;
                    lastResult = qrCodeMessage;
                    $.ajax({
                        url: base_url+"/loginBarcode/"+qrCodeMessage.toString(),
                        success: function (data) {
                            if(data.error) {
                                $("#error").show().html(data.message);
                                let timeout = setTimeout(function () {
                                    $("#error").hide();
                                    clearTimeout(timeout);
                                }, 5000);
                            } else {
                                $("#success").show().html(data.message);
                                let interval = setTimeout(function () {
                                    clearTimeout(interval);
                                    $("#success").hide();
                                    window.location.replace("{{url("/home")}}");
                                }, 2000);
                            }
                        },
                        error: function () {
                            $("#error").show().html("Something went wrong.");
                            let timeout = setTimeout(function () {
                                $("#error").hide();
                                clearTimeout(timeout);
                            }, 5000);
                        }
                    });
                }
            }

            var html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", { fps: 10, qrbox: 250 });
            html5QrcodeScanner.render(onScanSuccess);
        });
    </script>
@endsection