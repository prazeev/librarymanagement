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
    <script src="{{url('js/qr.min.transaction.js')}}"></script>
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
                        url: base_url+"/transaction/book/"+qrCodeMessage.toString(),
                        success: function (data) {
                            if(data.error) {
                                $("#error").show().html(data.message);
                                let timeout = setTimeout(function () {
                                    $("#error").hide();
                                    clearTimeout(timeout);
                                }, 10000);
                            } else {
                                $("#success").show().html(data.message);
                                let interval = setTimeout(function () {
                                    clearTimeout(interval);
                                    $("#success").hide();
                                }, 10000);
                            }
                        },
                        error: function(xhr, status, error) {
                            var err = eval("(" + xhr.responseText + ")");
                            alert(err);
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