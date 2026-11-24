<?php

use App\Http\Controllers\LibraryController as Library;
use Illuminate\Support\Facades\Route;

Route::get('/', [Library::class,'home'])->name('home');
Route::get('/catalogo', [Library::class,'catalog'])->name('catalog');
Route::get('/equipe', [Library::class,'team'])->name('team');
Route::middleware('guest')->group(function(){ Route::get('/entrar',[Library::class,'loginForm'])->name('login'); Route::post('/entrar',[Library::class,'login']); Route::post('/cadastro',[Library::class,'register'])->name('register'); });
Route::post('/sair',[Library::class,'logout'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function(){
    Route::get('/painel',[Library::class,'dashboard'])->name('dashboard');
    Route::get('/meus-emprestimos',[Library::class,'loans'])->name('loans');
    Route::middleware('admin')->group(function(){
        Route::get('/livros',[Library::class,'books'])->name('books');
        Route::post('/livros',[Library::class,'saveBook'])->name('books.create');
        Route::put('/livros/{book}',[Library::class,'saveBook'])->name('books.update');
        Route::delete('/livros/{book}',[Library::class,'deleteBook'])->name('books.delete');
        Route::get('/usuarios',[Library::class,'users'])->name('users');
        Route::post('/usuarios',[Library::class,'saveUser'])->name('users.create');
        Route::put('/usuarios/{user}',[Library::class,'saveUser'])->name('users.update');
        Route::delete('/usuarios/{user}',[Library::class,'deleteUser'])->name('users.delete');
        Route::post('/emprestimos',[Library::class,'createLoan'])->name('loans.create');
        Route::put('/emprestimos/{loan}',[Library::class,'updateLoan'])->name('loans.update');
        Route::post('/emprestimos/{loan}/devolver',[Library::class,'returnLoan'])->name('loans.return');
        Route::delete('/emprestimos/{loan}',[Library::class,'deleteLoan'])->name('loans.delete');
    });
});
