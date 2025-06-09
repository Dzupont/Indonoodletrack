a<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>
   Indo Noodle Track Monitoring
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
  <style>
   body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f8fb;
      margin: 0;
    }
    .sidebar {
        width: 250px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #4a9bb1;
        color: white;
        padding: 20px;
    }
    .sidebar h4 {
        font-weight: bold;
        font-size: 1.5rem;
        margin-bottom: 2rem;
    }
    .sidebar .nav-link {
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border-radius: 8px;
        margin: 5px 0;
        transition: all 0.3s ease;
    }
    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }
    .sidebar .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    .content {
        margin-left: 270px;
        padding: 30px;
    }
  </style>
 </head>
 <body>
  <!-- Sidebar -->
    <div class="sidebar">
        <h4>indo noodle track.</h4>
        <a class="nav-link" href="dashboardproduksi.php">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a class="nav-link" href="permintaanmasuk.php">
            <i class="fas fa-inbox"></i>
            <span>Pengajuan Bahan Baku</span>
        </a>
        <a class="nav-link" href="returbahanbaku.php">
            <i class="fas fa-undo"></i>
            <span>Retur Bahan Baku</span>
        </a>
        <a class="nav-link active" href="monitor.php">
            <i class="fas fa-eye"></i>
            <span>Monitoring</span>
        </a>
        <a class="nav-link" href="riwayat.php">
            <i class="fas fa-history"></i>
            <span>Riwayat</span>
        </a>
        <a class="nav-link" href="../../../views/auth/logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
   <div class="content">
   <header class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <h1 class="text-[#4a9bb1] font-extrabold text-2xl md:text-3xl leading-tight">
     Monitoring
    </h1>
    <form class="mt-4 md:mt-0 relative w-full max-w-xs md:max-w-sm" role="search">
     <input aria-label="Search" class="w-full rounded-full bg-[#d4f0f5] py-1.5 px-3 text-xs placeholder-[#4a9bb1] focus:outline-none" placeholder="Search" type="search"/>
     <button aria-label="Search" class="absolute right-2 top-1/2 -translate-y-1/2 text-[#4a9bb1] text-xs" type="submit">
      <i class="fas fa-search">
      </i>
     </button>
    </form>
    <div class="mt-6 md:mt-0 flex items-center space-x-3 whitespace-nowrap text-right text-xs text-[#3a3a3a]">
     <img alt="Photo of a young man with black hair wearing a white shirt and black tie" class="rounded-full w-8 h-8 object-cover" height="32" src="https://storage.googleapis.com/a1aa/image/8dbe71cf-05a3-4513-1631-9c930006e72c.jpg" width="32"/>
     <div>
      <div class="font-semibold text-sm">
       Divisi Gudang
      </div>
      <div class="text-xs text-[#7a7a7a]">
       User id : 02081999
      </div>
     </div>
    </div>
   </header>
   <section>
    <h2 class="font-bold text-sm mb-4">
     Order ID :
     <span class="font-extrabold text-sm">
      TXNID983274
     </span>
    </h2>
    <div class="bg-[#d4f0f5] rounded-2xl p-4 flex flex-col md:flex-row md:space-x-6">
     <!-- Left content -->
     <div class="flex-1">
      <div class="flex items-center space-x-2 mb-3">
       <div aria-hidden="true" class="w-3 h-3 rounded-full bg-[#e49e4a] flex-shrink-0">
       </div>
       <span class="font-semibold text-[#7a7a7a] text-xs">
        With driver
       </span>
      </div>
      <div class="flex flex-col md:flex-row md:space-x-4 space-y-3 md:space-y-0 mb-4">
       <div class="bg-white rounded-lg p-3 flex flex-col items-center w-full md:w-32 shadow">
        <i aria-hidden="true" class="fas fa-clock text-[#4a4a6a] text-lg mb-1.5 bg-[#d4d4e7] p-2 rounded-lg">
        </i>
        <div class="text-[9px] text-[#7a7a7a] mb-1">
         Order Placed
        </div>
        <div class="font-semibold text-sm">
         0
        </div>
       </div>
       <div class="bg-white rounded-lg p-3 flex flex-col items-center w-full md:w-32 shadow">
        <i aria-hidden="true" class="fas fa-shopping-cart text-[#4a4a6a] text-lg mb-1.5 bg-[#d4d4e7] p-2 rounded-lg">
        </i>
        <div class="text-[9px] text-[#7a7a7a] mb-1">
         Shipped
        </div>
        <div class="font-semibold text-sm">
         20
        </div>
       </div>
       <div class="bg-white rounded-lg p-3 flex flex-col items-center w-full md:w-32 shadow">
        <i aria-hidden="true" class="fas fa-box text-[#4a4a6a] text-lg mb-1.5 bg-[#d4d4e7] p-2 rounded-lg">
        </i>
        <div class="text-[9px] text-[#7a7a7a] mb-1">
         Completed
        </div>
        <div class="font-semibold text-sm">
         1500
        </div>
       </div>
      </div>
      <hr class="border-[#7a7a7a] border-opacity-20 mb-4"/>
      <div class="flex flex-col md:flex-row md:space-x-4 mb-4">
       <div class="bg-white rounded-lg p-3 flex-1 relative shadow max-w-md mb-3 md:mb-0">
        <div class="flex justify-between items-start mb-1">
         <div class="font-semibold text-xs">
          Shipping Address (Warehouse)
         </div>
         <button aria-label="Edit Shipping Address Warehouse" class="text-black hover:text-gray-700 text-xs">
          <i class="fas fa-edit">
          </i>
         </button>
        </div>
        <p class="text-[9px] text-[#7a7a7a] leading-tight">
         Kampus A Telkom
         <br/>
         Jl. Daan Mogot No.KM. 11
         <br/>
         Jakarta Barat, 11710,
         <br/>
         Indonesia
        </p>
       </div>
       <div class="bg-white rounded-lg p-3 flex-1 relative shadow max-w-md">
        <div class="flex justify-between items-start mb-1">
         <div class="font-semibold text-xs">
          Shipping Address (Production)
         </div>
         <button aria-label="Edit Shipping Address Production" class="text-black hover:text-gray-700 text-xs">
          <i class="fas fa-edit">
          </i>
         </button>
        </div>
        <p class="text-[9px] text-[#7a7a7a] leading-tight">
         Kampus B Telkom
         <br/>
         Jl. Halimun Raya No.2
         <br/>
         Jakarta Barat, 12980,
         <br/>
         Indonesia
        </p>
       </div>
      </div>
      <hr class="border-[#7a7a7a] border-opacity-20 mb-4"/>
      <div>
       <h3 class="font-semibold text-[#7a7a7a] mb-3 text-xs">
        Order Item
       </h3>
       <ul class="space-y-4">
        <li class="flex items-center justify-between text-xs">
         <div class="flex items-center space-x-3">
          <img alt="Bag of Tepung Terigu flour with blue and white packaging" class="w-10 h-10 object-contain bg-white p-1 rounded" height="40" src="https://storage.googleapis.com/a1aa/image/08853972-e88a-4c9d-fcae-0e055773f69e.jpg" width="40"/>
          <div>
           <div class="text-[11px] font-normal">
            Tepung Terigu
           </div>
           <div class="text-[9px] text-[#7a7a7a]">
            Exp : 08/2026
           </div>
          </div>
         </div>
         <div class="text-[11px] font-normal">
          100 Kg
         </div>
        </li>
        <li class="flex items-center justify-between text-xs">
         <div class="flex items-center space-x-3">
          <img alt="Carton of brown chicken eggs" class="w-10 h-10 object-contain bg-white p-1 rounded" height="40" src="https://storage.googleapis.com/a1aa/image/c0de3214-ea3f-4054-9ec8-3559196cd6ae.jpg" width="40"/>
          <div>
           <div class="text-[11px] font-normal">
            Telur Ayam
           </div>
           <div class="text-[9px] text-[#7a7a7a]">
            Exp : 08/2026
           </div>
          </div>
         </div>
         <div class="text-[11px] font-normal">
          300 btr
         </div>
        </li>
        <li class="flex items-center justify-between text-xs">
         <div class="flex items-center space-x-3">
          <img alt="Bottle of cooking oil with yellow liquid inside" class="w-10 h-10 object-contain bg-white p-1 rounded" height="40" src="https://storage.googleapis.com/a1aa/image/d5a7ac75-631d-4a66-9ae8-bbb8426298ad.jpg" width="40"/>
          <div>
           <div class="text-[11px] font-normal">
            Minyak Goreng
           </div>
           <div class="text-[9px] text-[#7a7a7a]">
            Exp : 08/2026
           </div>
          </div>
         </div>
         <div class="text-[11px] font-normal">
          500 ltr
         </div>
        </li>
       </ul>
      </div>
     </div>
     <!-- Right content -->
     <aside class="border-l border-[#4a9bb1] border-opacity-30 pl-4 mt-6 md:mt-0 w-full md:w-56 flex-shrink-0 text-xs text-[#4a9bb1]">
      <h4 class="font-semibold text-[11px] mb-5">
       Raw Material Journey
      </h4>
      <ol class="relative border-l border-[#4a9bb1] border-opacity-30 ml-3 space-y-6">
       <li class="relative pl-5">
        <span aria-hidden="true" class="absolute -left-3 top-1 w-2.5 h-2.5 rounded-full border border-[#4a9bb1] border-opacity-30 bg-transparent">
        </span>
        <div class="font-semibold text-[10px]">
         Diterima Produksi
        </div>
        <time class="block text-[8px] opacity-50">
         22/08/2022 15:24
        </time>
       </li>
       <li class="relative pl-5">
        <span aria-hidden="true" class="absolute -left-3 top-1 w-2.5 h-2.5 rounded-full border border-[#4a9bb1] border-opacity-30 bg-transparent">
        </span>
        <div class="font-semibold text-[10px]">
         Sampai di Produksi
        </div>
        <time class="block text-[8px] opacity-50">
         22/08/2022 15:24
        </time>
       </li>
       <li class="relative pl-5">
        <span aria-hidden="true" class="absolute -left-3 top-1 w-2.5 h-2.5 rounded-full border border-[#4a9bb1] border-opacity-100 bg-[#4a9bb1]">
        </span>
        <div class="font-semibold text-[10px]">
         Dikirim ke Produksi
        </div>
        <time class="block text-[8px] opacity-50">
         22/08/2022 15:24
        </time>
       </li>
       <li class="relative pl-5">
        <span aria-hidden="true" class="absolute -left-3 top-1 w-2.5 h-2.5 rounded-full border border-[#4a9bb1] border-opacity-100 bg-[#4a9bb1]">
        </span>
        <div class="font-semibold text-[10px]">
         Pemeriksaan QC
        </div>
        <time class="block text-[8px] opacity-50">
         22/08/2022 15:24
        </time>
       </li>
       <li class="relative pl-5">
        <span aria-hidden="true" class="absolute -left-3 top-1 w-2.5 h-2.5 rounded-full border border-[#4a9bb1] border-opacity-100 bg-[#4a9bb1]">
        </span>
        <div class="font-semibold text-[10px]">
         Gudang Bahan
        </div>
        <time class="block text-[8px] opacity-50">
         22/08/2022 15:24
        </time>
       </li>
      </ol>
     </aside>
    </div>
   </section>
  </main>
 </body>
</html>