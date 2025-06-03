<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>
   Monitoring
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
  <style>
   body {
      font-family: "Poppins", sans-serif;
    }
  </style>
 </head>
 <body class="bg-white">
  <div class="flex min-h-screen max-h-screen overflow-hidden">
   <!-- Sidebar -->
   <aside class="bg-[#4A9AB7] w-72 flex flex-col items-center py-10 space-y-10 rounded-tr-3xl rounded-br-3xl select-none">
    <img alt="Indo Noodle Track logo in white" class="mb-6" height="64" src="https://storage.googleapis.com/a1aa/image/1e1af7f1-ba7f-40f2-e6e3-7846efac2ad0.jpg" width="64"/>
    <nav class="flex flex-col space-y-6 w-full px-8 text-white font-semibold text-sm">
     <a class="flex items-center space-x-3" href="#">
      <i class="fas fa-home text-lg">
      </i>
      <span>
       Dashboard
      </span>
     </a>
     <a class="flex items-center space-x-3" href="#">
      <i class="fas fa-file-invoice text-lg">
      </i>
      <span>
       Permintaan Masuk
      </span>
     </a>
     <a class="flex items-center space-x-3" href="#">
      <i class="fas fa-sync-alt text-lg">
      </i>
      <span>
       Retur Masuk
      </span>
     </a>
     <a aria-current="page" class="flex items-center space-x-3 text-[#C9E9F0] font-bold" href="#">
      <i class="fas fa-cube text-lg">
      </i>
      <span>
       Monitoring
      </span>
     </a>
     <a class="flex items-center space-x-3" href="#">
      <i class="fas fa-search text-lg">
      </i>
      <span>
       Riwayat
      </span>
     </a>
    </nav>
   </aside>
   <!-- Main content -->
   <main class="flex-1 flex flex-col bg-white rounded-tl-3xl rounded-bl-3xl overflow-hidden">
    <!-- Header -->
    <header class="flex items-center justify-between px-8 py-6 border-b border-gray-200 select-none">
     <h1 class="text-3xl font-extrabold text-[#4A9AB7]">
      Monitoring
     </h1>
     <div class="relative w-64">
      <input class="w-full rounded-full bg-[#D4F1F7] text-[#4A9AB7] text-xs py-1.5 px-5 focus:outline-none" placeholder="Search" type="search"/>
      <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-[#4A9AB7] text-xs">
      </i>
     </div>
     <div class="flex items-center space-x-3">
      <img alt="User profile photo of a man with dark hair wearing a suit and tie" class="rounded-full w-10 h-10 object-cover" height="40" src="https://storage.googleapis.com/a1aa/image/bb8b71b3-3194-4a7e-0787-7eea05cdebe9.jpg" width="40"/>
      <div>
       <p class="text-[#2E1E1E] font-semibold text-sm leading-5">
        Divisi Produksi
       </p>
       <p class="text-[#8B8B8B] text-xs leading-4">
        User id : 23032000
       </p>
      </div>
     </div>
    </header>
    <!-- Content -->
    <section class="flex flex-1 overflow-hidden">
     <!-- Left content -->
     <section class="flex-1 bg-[#D4F1F7] p-6 overflow-y-auto rounded-tr-3xl rounded-br-3xl">
      <h2 class="text-base font-bold mb-4">
       Order ID :
       <span class="font-extrabold">
        TXNID983274
       </span>
      </h2>
      <!-- With driver label -->
      <div class="flex items-center mb-3 space-x-2">
       <div class="w-3.5 h-3.5 rounded-full bg-[#D98B2B]">
       </div>
       <p class="text-[#9E9E9E] font-semibold text-sm">
        With driver
       </p>
      </div>
      <!-- Status cards -->
      <div class="flex space-x-4 mb-5 max-w-4xl">
       <div class="bg-white rounded-lg p-4 flex items-center space-x-3 w-36 shadow-sm">
        <div class="bg-[#E6E9F7] p-2.5 rounded-lg flex justify-center items-center">
         <i class="fas fa-chart-pie text-[#4A4A6A] text-lg">
         </i>
        </div>
        <div>
         <p class="text-[10px] text-[#9E9E9E] mb-0.5">
          Order Placed
         </p>
         <p class="text-sm font-normal text-black">
          0
         </p>
        </div>
       </div>
       <div class="bg-white rounded-lg p-4 flex items-center space-x-3 w-36 shadow-sm">
        <div class="bg-[#E6E9F7] p-2.5 rounded-lg flex justify-center items-center">
         <i class="fas fa-shopping-cart text-[#4A4A6A] text-lg">
         </i>
        </div>
        <div>
         <p class="text-[10px] text-[#9E9E9E] mb-0.5">
          Shipped
         </p>
         <p class="text-sm font-normal text-black">
          20
         </p>
        </div>
       </div>
       <div class="bg-white rounded-lg p-4 flex items-center space-x-3 w-36 shadow-sm">
        <div class="bg-[#E6E9F7] p-2.5 rounded-lg flex justify-center items-center">
         <i class="fas fa-cube text-[#4A4A6A] text-lg">
         </i>
        </div>
        <div>
         <p class="text-[10px] text-[#9E9E9E] mb-0.5">
          Completed
         </p>
         <p class="text-sm font-semibold text-black">
          1500
         </p>
        </div>
       </div>
      </div>
      <hr class="border-t border-[#8BC6C9] mb-5"/>
      <!-- Shipping addresses -->
      <div class="flex space-x-4 mb-5 max-w-4xl">
       <div aria-label="Shipping Address Warehouse" class="bg-white rounded-lg p-4 shadow-md w-80 relative">
        <h3 class="font-semibold text-sm mb-1 flex justify-between items-center">
         Shipping Address (Warehouse)
         <button aria-label="Edit Shipping Address Warehouse" class="text-black text-lg">
          <i class="fas fa-edit">
          </i>
         </button>
        </h3>
        <p class="text-[10px] text-[#9E9E9E] leading-4">
         Kampus A Telkom
         <br/>
         Jl. Daan Mogot No.KM. 11
         <br/>
         Jakarta Barat, 11710,
         <br/>
         Indonesia
        </p>
       </div>
       <div aria-label="Shipping Address Production" class="bg-white rounded-lg p-4 shadow-md w-80 relative">
        <h3 class="font-semibold text-sm mb-1 flex justify-between items-center">
         Shipping Address (Production)
         <button aria-label="Edit Shipping Address Production" class="text-black text-lg">
          <i class="fas fa-edit">
          </i>
         </button>
        </h3>
        <p class="text-[10px] text-[#9E9E9E] leading-4">
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
      <hr class="border-t border-[#8BC6C9] mb-5"/>
      <!-- Order Item -->
      <h3 class="text-[#9E9E9E] font-semibold text-sm mb-5">
       Order Item
      </h3>
      <ul class="space-y-5 max-w-4xl">
       <li class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
         <img alt="Bag of Tepung Terigu flour with blue and white packaging" class="w-14 h-14 object-contain bg-white p-1 rounded" height="56" src="https://storage.googleapis.com/a1aa/image/d183ab41-34d7-4b79-d064-08242413e5c3.jpg" width="56"/>
         <div>
          <p class="text-xs text-[#2E2E2E]">
           Tepung Terigu
          </p>
          <p class="text-[9px] text-[#9E9E9E]">
           Exp : 08/2026
          </p>
         </div>
        </div>
        <p class="text-xs text-[#2E2E2E]">
         100 Kg
        </p>
       </li>
       <li class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
         <img alt="Carton of brown chicken eggs" class="w-14 h-14 object-contain bg-white p-1 rounded" height="56" src="https://storage.googleapis.com/a1aa/image/1d24e8fe-e3f2-451a-aaef-e0477c9e27fd.jpg" width="56"/>
         <div>
          <p class="text-xs text-[#2E2E2E]">
           Telur Ayam
          </p>
          <p class="text-[9px] text-[#9E9E9E]">
           Exp : 08/2026
          </p>
         </div>
        </div>
        <p class="text-xs text-[#2E2E2E]">
         300 btr
        </p>
       </li>
       <li class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
         <img alt="Bottle of cooking oil with yellow label" class="w-14 h-14 object-contain bg-white p-1 rounded" height="56" src="https://storage.googleapis.com/a1aa/image/362bc655-fb4d-48f4-d03b-83d38a39cd59.jpg" width="56"/>
         <div>
          <p class="text-xs text-[#2E2E2E]">
           Minyak Goreng
          </p>
          <p class="text-[9px] text-[#9E9E9E]">
           Exp : 08/2026
          </p>
         </div>
        </div>
        <p class="text-xs text-[#2E2E2E]">
         500 ltr
        </p>
       </li>
      </ul>
     </section>
     <!-- Right content -->
     <aside class="w-72 bg-[#D4F1F7] border-l border-[#8BC6C9] p-5 overflow-y-auto select-none">
      <h3 class="font-semibold text-sm mb-5">
       Raw Material Journey
      </h3>
      <ul class="space-y-6 text-[10px] text-[#2E2E2E]">
       <li class="flex items-start space-x-3 relative pl-5">
        <div class="absolute left-0 top-2 w-2.5 h-2.5 border border-[#8BC6C9] rounded-full">
        </div>
        <div class="flex flex-col">
         <p class="font-semibold">
          Diterima Produksi
         </p>
         <p class="text-[#8B8B8B] mt-0.5">
          22/08/2022 15:24
         </p>
        </div>
       </li>
       <li class="flex items-start space-x-3 relative pl-5">
        <div class="absolute left-0 top-2 w-2.5 h-2.5 border border-dotted border-[#8BC6C9] rounded-full">
        </div>
        <div class="flex flex-col">
         <p class="font-semibold">
          Sampai di Produksi
         </p>
         <p class="text-[#8B8B8B] mt-0.5">
          22/08/2022 15:24
         </p>
        </div>
       </li>
       <li class="flex items-start space-x-3 relative pl-5">
        <div class="absolute left-0 top-2 w-2.5 h-2.5 bg-[#4A9AB7] border border-[#4A9AB7] rounded-full flex justify-center items-center text-white">
         <i class="fas fa-check text-[7px]">
         </i>
        </div>
        <div class="flex flex-col">
         <p class="font-semibold">
          Dikirim ke Produksi
         </p>
         <p class="text-[#8B8B8B] mt-0.5">
          22/08/2022 15:24
         </p>
        </div>
       </li>
       <li class="flex items-start space-x-3 relative pl-5">
        <div class="absolute left-0 top-2 w-2.5 h-2.5 bg-[#4A9AB7] border border-[#4A9AB7] rounded-full flex justify-center items-center text-white">
         <i class="fas fa-check text-[7px]">
         </i>
        </div>
        <div class="flex flex-col">
         <p class="font-semibold">
          Pemeriksaan QC
         </p>
         <p class="text-[#8B8B8B] mt-0.5">
          22/08/2022 15:24
         </p>
        </div>
       </li>
       <li class="flex items-start space-x-3 relative pl-5">
        <div class="absolute left-0 top-2 w-2.5 h-2.5 bg-[#4A9AB7] border border-[#4A9AB7] rounded-full flex justify-center items-center text-white">
         <i class="fas fa-check text-[7px]">
         </i>
        </div>
        <div class="flex flex-col">
         <p class="font-semibold">
          Gudang Bahan
         </p>
         <p class="text-[#8B8B8B] mt-0.5">
          22/08/2022 15:24
         </p>
        </div>
       </li>
      </ul>
     </aside>
    </section>
   </main>
  </div>
 </body>
</html>