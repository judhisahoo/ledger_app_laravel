@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ url('/get_balance')}}">View Balance : <span id="showBal"></span></a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ url('/add-money')}}">Add Money</a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ url('/transfer-money')}}">Transfer Money</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function(){
        $('#viewBalance').click(function() {
            $.ajax({
                url: '/show-balance',
                type: 'POST',
                data: {
                    '_token': '{{csrf_token()}}'
                },
                success: function(data) {
                    console.log('get data ==', data);
                    $('#sishowBalgnin').html(data);
                }
            });
        })

    })
</script>
@endsection
