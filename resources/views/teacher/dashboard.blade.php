<x-app-layout>
  <div class="p-6">
    <h1 class="text-2xl font-bold">Teachers Dashboard</h1>
    <p>Welcome, {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
  </div>
</x-app-layout>
