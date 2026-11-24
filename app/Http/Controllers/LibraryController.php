<?php
namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LibraryController extends Controller
{
    public function home() { return view('home', ['books'=>Book::latest()->take(4)->get()]); }
    public function catalog(Request $request) {
        $books = Book::query()->when($request->q, fn($q, $term) => $q->where(fn($s)=>$s->where('title','like',"%$term%")->orWhere('author','like',"%$term%")))->when($request->category, fn($q,$c)=>$q->where('category',$c))->orderBy('title')->paginate(12)->withQueryString();
        return view('catalog', compact('books'));
    }
    public function team() { return view('team'); }
    public function loginForm() { return view('auth'); }
    public function login(Request $request) {
        $data = $request->validate(['email'=>'required|email','password'=>'required']);
        if (!Auth::attempt($data)) return back()->withErrors(['email'=>'E-mail ou senha inválidos.'])->onlyInput('email');
        $request->session()->regenerate(); return redirect()->intended(route('dashboard'));
    }
    public function register(Request $request) {
        $data = $request->validate(['name'=>'required|string|max:255','email'=>'required|email|unique:users,email','password'=>'required|string|min:8|confirmed']);
        $user = User::create([...$data,'password'=>Hash::make($data['password']),'role'=>'reader']); Auth::login($user);
        $request->session()->regenerate(); return redirect()->route('dashboard');
    }
    public function logout(Request $request) { Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect()->route('home'); }
    public function dashboard() {
        $loans = Loan::with(['book','user'])->when(!Auth::user()->is_admin, fn($q)=>$q->where('user_id',Auth::id()))->latest()->get();
        return view('dashboard', ['loans'=>$loans,'booksCount'=>Book::count(),'usersCount'=>User::count(),'activeCount'=>Loan::whereNull('returned_at')->count()]);
    }
    public function books() { return view('books', ['books'=>Book::orderBy('title')->get()]); }
    public function saveBook(Request $r, ?Book $book = null) {
        $data=$r->validate(['title'=>'required|max:255','author'=>'required|max:255','publisher'=>'nullable|max:255','category'=>'nullable|max:100','publication_year'=>'nullable|integer|min:1000|max:'.date('Y'),'isbn'=>'required|max:20|unique:books,isbn'.($book?','.$book->id:''),'copies'=>'required|integer|min:1']);
        DB::transaction(function() use($data,$book) { $item=$book ?? new Book; $loaned=$item->exists ? $item->copies-$item->available_copies : 0; if($data['copies']<$loaned) throw \Illuminate\Validation\ValidationException::withMessages(['copies'=>'O total não pode ser menor que os exemplares emprestados.']); $item->fill([...$data,'available_copies'=>$data['copies']-$loaned])->save(); });
        return back()->with('success','Livro salvo com sucesso.');
    }
    public function deleteBook(Book $book) { if($book->loans()->exists()) return back()->withErrors(['action'=>'Este livro possui histórico de empréstimos e não pode ser excluído.']); $book->delete(); return back()->with('success','Livro excluído.'); }
    public function users() { return view('users', ['users'=>User::orderBy('name')->get()]); }
    public function saveUser(Request $r, ?User $user = null) {
        $data=$r->validate(['name'=>'required|max:255','email'=>'required|email|unique:users,email'.($user?','.$user->id:''),'cpf'=>'nullable|max:14|unique:users,cpf'.($user?','.$user->id:''),'phone'=>'nullable|max:30','address'=>'nullable|max:255','role'=>'required|in:admin,reader','password'=>($user?'nullable':'required').'|nullable|min:8']);
        if(empty($data['password'])) unset($data['password']); else $data['password']=Hash::make($data['password']);
        ($user ?? new User)->fill($data)->save(); return back()->with('success','Usuário salvo com sucesso.');
    }
    public function deleteUser(User $user) { if($user->id===Auth::id()) return back()->withErrors(['action'=>'Você não pode excluir sua própria conta.']); if($user->loans()->exists()) return back()->withErrors(['action'=>'Este usuário possui histórico de empréstimos e não pode ser excluído.']); $user->delete(); return back()->with('success','Usuário excluído.'); }
    public function loans() { $loans=Loan::with(['book','user'])->when(!Auth::user()->is_admin,fn($q)=>$q->where('user_id',Auth::id()))->latest()->get(); return view('loans',['loans'=>$loans,'books'=>Book::where('available_copies','>',0)->orderBy('title')->get(),'users'=>User::orderBy('name')->get()]); }
    public function createLoan(Request $r) {
        $data=$r->validate(['user_id'=>'required|exists:users,id','book_id'=>'required|exists:books,id','loan_date'=>'required|date','due_date'=>'required|date|after_or_equal:loan_date']);
        DB::transaction(function() use($data) { $book=Book::whereKey($data['book_id'])->lockForUpdate()->firstOrFail(); if($book->available_copies<1) throw \Illuminate\Validation\ValidationException::withMessages(['book_id'=>'Não há exemplares disponíveis.']); $book->decrement('available_copies'); Loan::create([...$data,'status'=>'borrowed']); });
        return back()->with('success','Empréstimo registrado.');
    }
    public function returnLoan(Loan $loan) { if(!$loan->returned_at) DB::transaction(function()use($loan){ $loan->update(['returned_at'=>now()->toDateString(),'status'=>'returned']); $loan->book()->increment('available_copies'); }); return back()->with('success','Devolução registrada.'); }
    public function updateLoan(Request $r, Loan $loan) {
        abort_if($loan->returned_at, 422, 'Empréstimos já devolvidos não podem ser editados.');
        $data=$r->validate(['user_id'=>'required|exists:users,id','loan_date'=>'required|date','due_date'=>'required|date|after_or_equal:loan_date']);
        $loan->update($data); return back()->with('success','Empréstimo atualizado.');
    }
    public function deleteLoan(Loan $loan) { if(!$loan->returned_at) $loan->book()->increment('available_copies'); $loan->delete(); return back()->with('success','Empréstimo cancelado.'); }
}
