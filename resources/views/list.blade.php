@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Listagem de CNPJ</div>

                <div class="card-body">

                    <form action="{{ action('App\Http\Controllers\CNPJController@index') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="col-3">
                                <input type="text" class="form-control" placeholder="Pesquisar CNPJ" name="cnpj" />
                            </div>
                            <div class="col-3">
                                <input type="number" class="form-control" placeholder="Quantidade por página"
                                    name="qtd" />
                            </div>
                            <div class="col-2">
                                <button type="submit" class="btn btn-primary">Buscar</button>

                            </div>
                        </div>
                    </form>
                    <p>
                        <strong> Coloquei um delay de 20 segundos para cada busca de registro, devido a limitação do
                            site da Receita Federal</strong>
                    </p>
                    <br>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Nome</th>
                                <th scope="col">CNPJ</th>
                                <th scope="col">porte</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $dados)
                            <tr>
                                @if(!empty($dados->cnpj))
                                <td>{{$dados->nome}}</td>
                                <td>{{$dados->cnpj}}</td>
                                <td>{{$dados->porte}}</td>
                            </tr>
                            @else
                            <tr>
                                <td></td>
                                <td>try again!</td>
                                <td></td>
                            </tr>
                            @endif
                            @empty
                            <p>No data</p>
                            @endforelse
                        </tbody>
                    </table>
                    <nav aria-label="Navegação de página exemplo">
                        <ul class="pagination">
                            {{ $cnpjs->links() }}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
