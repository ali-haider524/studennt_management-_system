<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">
      Manage Attendance — {{ $course->code }} {{ $course->title }} ({{ $session->session_date }})
    </h2>
  </x-slot>

  <div class="p-6 space-y-4">
    @if(session('ok')) <div class="bg-green-100 text-green-800 px-3 py-2 rounded">{{ session('ok') }}</div> @endif

    <div class="bg-white shadow rounded overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="border-b">
          <tr class="text-left">
            <th class="p-3">Student</th>
            <th class="p-3">Current</th>
            <th class="p-3">Mark</th>
          </tr>
        </thead>
        <tbody>
          @foreach($students as $st)
            @php $m = $marks->get($st->id); @endphp
            <tr class="border-b">
              <td class="p-3">{{ $st->name }}</td>
              <td class="p-3">{{ $m->status ?? '-' }}</td>
              <td class="p-3">
                <form method="POST" action="{{ route('attendance.mark',$session) }}" class="flex gap-2 items-center">
                  @csrf
                  <input type="hidden" name="student_id" value="{{ $st->id }}">
                  <select name="status" class="border p-1 rounded">
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="late">Late</option>
                  </select>
                  <button class="bg-gray-800 text-white px-3 py-1 rounded">Save</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</x-app-layout>
