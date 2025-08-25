<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Attendance — {{ $course->code }} {{ $course->title }}</h2>
  </x-slot>

  <div class="p-6 space-y-6">
    @if(session('ok')) <div class="bg-green-100 text-green-800 px-3 py-2 rounded">{{ session('ok') }}</div> @endif

    @if(in_array(auth()->user()->role,['admin','teacher']))
    <div class="bg-white shadow rounded p-4">
      <h3 class="font-semibold mb-3">Create Session</h3>
      <form method="POST" action="{{ route('attendance.sessions.store',$course) }}" class="grid md:grid-cols-3 gap-3">
        @csrf
        <input class="border p-2 rounded md:col-span-2" name="title" placeholder="e.g., Week 1 - Intro" required>
        <input type="date" class="border p-2 rounded" name="session_date" required>
        <input type="time" class="border p-2 rounded" name="starts_at">
        <input type="time" class="border p-2 rounded" name="ends_at">
        <button class="bg-blue-600 text-white px-4 py-2 rounded md:col-span-3">Create</button>
      </form>
    </div>
    <div class="bg-white shadow rounded p-4">
  <h3 class="font-semibold mb-3">Bulk Create (Weekly)</h3>
  <form method="POST" action="{{ route('attendance.sessions.bulk',$course) }}" class="grid md:grid-cols-3 gap-3">
    @csrf
    <input class="border p-2 rounded md:col-span-3" name="title" placeholder="e.g., Lecture" required>
    <input type="date" class="border p-2 rounded" name="start_date" required>
    <input type="date" class="border p-2 rounded" name="end_date" required>

    <div class="md:col-span-3 flex flex-wrap gap-3">
      @php $days=[ 'mon'=>'Mon','tue'=>'Tue','wed'=>'Wed','thu'=>'Thu','fri'=>'Fri','sat'=>'Sat','sun'=>'Sun']; @endphp
      @foreach($days as $k=>$v)
        <label class="flex items-center gap-2">
          <input type="checkbox" name="weekdays[]" value="{{ $k }}"> {{ $v }}
        </label>
      @endforeach
    </div>

    <input type="time" class="border p-2 rounded" name="starts_at">
    <input type="time" class="border p-2 rounded" name="ends_at">
    <button class="bg-blue-600 text-white px-4 py-2 rounded md:col-span-3">Generate</button>
    </form>
    </div>

    @endif

    <div class="bg-white shadow rounded">
      <table class="min-w-full text-sm">
        <thead class="border-b">
          <tr class="text-left">
            <th class="p-3">Date</th>
            <th class="p-3">Title</th>
            <th class="p-3">Time</th>
            <th class="p-3">Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($sessions as $s)
          <tr class="border-b">
            <td class="p-3">{{ $s->session_date }}</td>
            <td class="p-3">{{ $s->title }}</td>
            <td class="p-3">
              {{ $s->starts_at ?? '--' }} {{ $s->ends_at ? ' - '.$s->ends_at : '' }}
            </td>
            <td class="p-3">
              @if(in_array(auth()->user()->role,['admin','teacher']))
                <a class="text-blue-600 underline" href="{{ route('attendance.manage',$s) }}">Manage</a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td class="p-3" colspan="4">No sessions yet.</td></tr>
        @endforelse
        </tbody>
      </table>
      <div class="p-3">{{ $sessions->links() }}</div>
    </div>
  </div>
</x-app-layout>
