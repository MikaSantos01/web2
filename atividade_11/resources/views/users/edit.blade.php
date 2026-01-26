@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Editar Usuário</h1>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>

        {{-- Apenas admin pode alterar o papel --}}
        @if(auth()->user()->isAdmin())
            <div class="mb-3">
                <label for="role" class="form-label">Papel</label>
                <select name="role" id="role" class="form-select">
                    <option value="{{ \App\Models\User::ROLE_CLIENTE }}" {{ $user->role === 'cliente' ? 'selected' : '' }}>Cliente</option>
                    <option value="{{ \App\Models\User::ROLE_BIBLIOTECARIO }}" {{ $user->role === 'bibliotecario' ? 'selected' : '' }}>Bibliotecário</option>
                    <option value="{{ \App\Models\User::ROLE_ADMIN }}" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
        @endif

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
