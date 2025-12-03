<?php

namespace App\Http\Controllers;

use App\Models\KategoriItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KategoriItemsController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('kategori_items.index.index');
   }

   public function search(Request $request)
   {
      $kode = $request->kode;
      $nama = $request->nama;


      $data_search = KategoriItem::query();

      if (!empty($kode)) $data_search = $data_search->where('kode', $kode);
      if (!empty($nama)) $data_search = $data_search->where('nama', 'LIKE', '%' . $nama . '%');

      $data_search = $data_search->select('kode', 'nama')->orderBy('id')->get();


      return json_encode([
         'status' => 200,
         'data' => $data_search
      ]);
   }

   public function formView($method, $id = 0)
   {
      if ($method == 'new') {
         $item = [];
      } else {
         $item = KategoriItem::find($id);
      }
      $data['item'] = $item;
      $data['method'] = $method;
      return view('kategori_items.form.index', $data);
   }

   public function singleView($kode)
   {
      $data['data'] = KategoriItem::where('kode', $kode)->first();
      die(dd($data['data']));
      return view('kategori_items.single.index', $data);
   }

   public function formSubmit(Request $request, $method, $id = 0)
   {
      if ($method == 'new') {
         $data_item = new KategoriItem();
         $kode = KategoriItem::count('id');
         $kode = 'K' . $kode + 1;
         $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);
         sleep(3);
      } else {
         $data_item = KategoriItem::find($id);
         $kode = $data_item->kode;
      }

      $data_item->nama = $request->nama;
      $data_item->kode = $kode;
      // dd($data_item);
      // die();
      $data_item->save();

      return redirect('kategori-items');
   }

   public function delete($id)
   {
      KategoriItem::find($id)->delete();
      return redirect('kategori-items');
   }

   // public function cetakPdf()
   // {
   //    $categories = KategoriItem::all();
   //    $cetak_pada = now()->translatedFormat('d F Y, H:i:s');

   //    // 2. Buat view untuk layout PDF (misalnya 'items.pdf_template')
   //    $pdf = Pdf::loadView('kategori_items.index.pdf_template', compact('categories'));

   //    // 3. Kembalikan PDF sebagai unduhan
   //    // 'master_items.pdf' adalah nama file yang akan diunduh
   //    return $pdf->download('kategori-items.pdf');
   // }
   public function cetakPdf()
{
    // Ambil SEMUA Kategori, beserta Item-item yang berelasi (Eager Loading)
    // Asumsikan relasi di Model Category bernama 'items'
    $data = KategoriItem::all(); 
    
    // Tentukan waktu cetak untuk footer
    $cetak= now()->translatedFormat('d F Y, H:i:s'); // Contoh format waktu Indonesia

    // Load View dengan data kategori dan waktu cetak
    $pdf = Pdf::loadView('kategori_items.index.pdf_template',compact('data','cetak'));
    
    // Opsional: Atur ukuran dan orientasi kertas (misal A4 Landscape)
    // $pdf->setPaper('A4', 'landscape');

    return $pdf->download('laporan_item_per_kategori.pdf');
}

   public function updateRandomData()
   {
      $data = KategoriItem::get();
      foreach ($data as $item) {
         $kode = $item->id;
         $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);
         $item->kode = $kode;
         $item->nama = $item->nama;
         $item->save();
      }
   }
}
