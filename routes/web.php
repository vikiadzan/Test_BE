<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('Home',[
                'nama'=>'Vicky Adzandra',
                'nobp'=>'2001092068',
                'jk'=>'Laki-Laki',
                'no_tlp'=>'082090988776',
                'email'=>'vivikai@gmail.com',
                'alamat'=>'Solok'
    ]);
});

Route::get('/welcome', function () {
    return view('welcome');
});


// Route::get('/datasaya/{nama}/{nobp}',function($nama,$nobp){
//     return"<h1> Nama saya: $nama</h1>
            
//     <h1> Nama saya: $nobp</h1>
//     ";
// });

// Route::get('/databarang/{kat?}/{namabarang?}',function($x="komputer",$y="asus"){

//     return"<h1> Data barang : $x</h1>
          
//   <h1> Nama Barang: $y</h1>
//   ";
// });

// Route::get('/user2/{id}',function($id){
//     return "tampilkan user dengan id = $id";
// })->where('id','[A-Z]{2}[0-9]+');
