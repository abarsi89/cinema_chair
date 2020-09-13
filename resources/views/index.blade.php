@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td class="text-center">Vászon</td>
                            </tr>
                        </tbody>
                    </table>

                    <br/>

                    <table class="table table-bordered text-center">
                        <tbody>
                            @foreach($chairs as $chair)
                                @if ($chair->number === 1)
                                    <tr style="height: 120px;">
                                        <td style="width: 12px; background-color: grey;">
                                            {{ $chair->row }}.
                                            sor
                                        </td>
                                @endif

                                @if ($chair->status === 'free')
                                    <td style="background-color: LimeGreen;">
                                        <a href="{{ route('reserve', $chair->id) }}">
                                            {{ $chair->number }}. szék
                                        </a>
                                        <br/>
                                        {{ __('messages.'.$chair->status) }}
                                    </td>

                                @elseif ($chair->status === 'reserved')
                                    <td style="background-color: Gold;">
                                        <a href="{{ route('reserve', $chair->id) }}">
                                            {{ $chair->number }}. szék
                                        </a>
                                        <br/>
                                        {{ __('messages.'.$chair->status) }}
                                    </td>
                                @elseif ($chair->status === 'sold')
                                    <td style="background-color: OrangeRed;">
                                        {{ $chair->number }}. szék
                                        <br/>
                                        {{ __('messages.'.$chair->status) }}
                                    </td>
                                @endif

                                @if ($chair->number === 6)
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                        Fizetés
                    </button>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Fizetés</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form role="form" method="POST" action="{{ route('payment') }}">
                <div class="modal-body">
                    <label class="control-label">Kérjük add meg email címedet fizetésképpen:</label>
                    <div class="form-group">
                        <input type="email" name="email" id="email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Vissza</button>
                    @csrf
                    <button type="submit" class="btn btn-primary">Elküld</button>
                </div>
            </form>
        </div>
    </div>
  </div>

@endsection
