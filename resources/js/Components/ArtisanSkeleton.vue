<template>
  <div class="animate-pulse container mx-auto pt-16">
    <div class="bg-white rounded-[24px] p-8 md:p-12 shadow-sm border border-slate-100 flex flex-col md:flex-row gap-12">
      <!-- Avatar Skeleton -->
      <div class="w-full md:w-[400px] h-[400px] bg-slate-200 rounded-2xl shrink-0"></div>
      
      <!-- Info Skeleton -->
      <div class="flex-1 space-y-6">
        <div class="w-32 h-8 bg-slate-100 rounded-full"></div>
        <div class="w-3/4 h-12 bg-slate-200 rounded-xl"></div>
        <div class="w-1/2 h-6 bg-slate-100 rounded-md"></div>
        
        <div class="space-y-4 pt-4">
          <div class="w-full h-4 bg-slate-50 rounded"></div>
          <div class="w-full h-4 bg-slate-50 rounded"></div>
          <div class="w-4/5 h-4 bg-slate-50 rounded"></div>
        </div>
        
        <div class="pt-8">
          <div class="w-48 h-14 bg-slate-200 rounded-xl"></div>
        </div>
      </div>
    </div>
  </div>
</template>
