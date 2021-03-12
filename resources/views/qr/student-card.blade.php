<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ID Card</title>
    <style>
        #card {
            background-color: #ffffff;
            height: 400px;
            width: 800px;
        }
        img {
            width: 200px;
            height: 200px;
            object-fit: contain;
        }
        #card-header {
            height: 60px;
            width: 100%;
            background-color: #0d6aad;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #card-header p {
            letter-spacing: 5px;
            color: #ffffff;
            font-size: 30px;
            font-weight: 400;
        }
        #card-content {
            padding: 5px;
            display: flex;
            flex-direction: row;
            background-color: #ffffff;
            border:1px solid #0d6aad;
        }
        #card-content #qr-holder {

        }
        #card-content #photo-holder {

        }
        #card-content #student-details {
            padding-left: 5px;
            flex-direction: column;
            display: flex;
            justify-content: space-between;
            flex:1;
        }
        #card-content #student-details #student-name {
            font-weight: bold;
            font-size: 24px;
            display: flex;
        }
        #card-content #student-details .student-other {
            font-weight: normal;
            font-size: 20px;
            display: flex;
        }
        #card-content #student-details .student-other .student-departments {
            display: flex;
        }
        #card-content #student-details .student-other .student-other-email {
            font-size: 10px;
            color: #000000;
        }
        #card-content #student-details .student-other .student-departments .student-department {
            background-color: green;
            color: #ffffff;
            padding: 5px;
            margin: 2px;
            font-size: 14px;
            border-radius: 5px;
        }

    </style>
</head>
<body>
    <div id="card">
        <div id="card-header">
            <p>STUDENT CARD</p>
        </div>
        <div id="card-body">
            <div id="card-content">
                <div id="qr-holder">
                    {!! QrCode::size(200)->generate(\Illuminate\Support\Facades\Crypt::encryptString($student->email)) !!}
                </div>
                <div id="student-details">
                    <div id="student-name">{{$student->name}}</div>
                    <div class="student-other">ID: #{{$student->id}}</div>
                    <div class="student-other student-other-email"><i>{{$student->email}}</i></div>
                    <div class="student-other">
                        <div class="student-departments">
                            @foreach($student->departments as $department)
                                <div class="student-department">{{$department->name}}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @if(!empty($student->picture))
                <div id="photo-holder">
                    <img src="{{\Illuminate\Support\Facades\Storage::url($student->picture)}}">
                </div>
                @endif
            </div>
        </div>
    </div>
    <input type="button" onclick="printDiv('card')" value="Print" />
</body>
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>
</html>