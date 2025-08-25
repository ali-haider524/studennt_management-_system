<x-app-layout>
  <div class="p-6 space-y-8">
    <x-slot name="header">
      <h2 class="font-semibold text-xl leading-tight">
        {{ $course->code }} — {{ $course->title }} : Assignments
      </h2>
    </x-slot>

    @if(session('ok'))
      <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('ok') }}</div>
    @endif

    @if(in_array(auth()->user()->role, ['admin','teacher']))
      <div class="bg-white shadow rounded p-4">
        <h3 class="font-semibold mb-3">New Assignment</h3>
        <form method="POST" action="{{ route('assignments.store',$course) }}" class="grid gap-3 md:grid-cols-2">
          @csrf
          <input class="border p-2 rounded" name="title" placeholder="Title" required>
          <select class="border p-2 rounded" name="visibility">
            <option value="published" selected>Published</option>
            <option value="draft">Draft</option>
            <option value="closed">Closed</option>
          </select>
          <input type="datetime-local" class="border p-2 rounded" name="due_at">
          <textarea class="border p-2 rounded md:col-span-2" name="description" placeholder="Description"></textarea>
          <input type="datetime-local" class="border p-2 rounded" name="open_at">
          <input type="datetime-local" class="border p-2 rounded" name="due_at">
          <input type="datetime-local" class="border p-2 rounded" name="close_at">
          <label class="flex items-center gap-2">
          <input type="checkbox" name="accept_late" value="1"> Accept late submissions
          </label>
         <input type="datetime-local" class="border p-2 rounded" name="late_until" placeholder="Late until">

          <button class="bg-blue-600 text-white px-4 py-2 rounded md:col-span-2">Create</button>
        </form>
      </div>
    @endif

    <div class="space-y-4">
      @foreach($assignments as $a)
        <div class="bg-white shadow rounded p-4">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold text-lg">{{ $a->title }}</h3>
            <div class="text-xs text-gray-500">
              Status: {{ $a->visibility }}
              @if($a->due_at) • Due: {{ $a->due_at->format('Y-m-d H:i') }} @endif
            </div>
            <div class="text-xs text-gray-500 mt-1">
            @if($a->open_at) Opens: {{ $a->open_at->format('Y-m-d H:i') }} • @endif
            @if($a->due_at) Due: {{ $a->due_at->format('Y-m-d H:i') }} • @endif
            @if($a->close_at) Closes: {{ $a->close_at->format('Y-m-d H:i') }} @endif
            </div>

          </div>
          <p class="mt-2 text-gray-700">{{ $a->description }}</p>

          @if(auth()->user()->role==='student' && $a->visibility!=='draft')
            <form method="POST" action="{{ route('submissions.store',$a) }}" enctype="multipart/form-data" class="mt-3 flex flex-col md:flex-row gap-2">
              @csrf
              <input type="file" name="file" class="border p-2 rounded">
              <input type="text" name="notes" class="border p-2 rounded flex-1" placeholder="Notes (optional)">
              <button class="bg-emerald-600 text-white px-3 py-2 rounded">Submit</button>
            </form>
          @endif

          @php
            $me = auth()->user();
            $subs = $a->submissions()->with('student')
                   ->when($me->role==='student', fn($q)=>$q->where('student_id',$me->id))
                   ->get();
          @endphp

          @if($subs->count())
            <div class="overflow-x-auto mt-4">
              <table class="min-w-full text-sm">
                <thead><tr class="text-left border-b">
                  <th class="py-1 pr-2">Student</th>
                  <th class="py-1 pr-2">Status</th>
                  <th class="py-1 pr-2">Grade</th>
                  <th class="py-1 pr-2">File</th>
                  @if(in_array($me->role,['admin','teacher']))<th class="py-1 pr-2">Actions</th>@endif
                </tr></thead>
                <tbody>
                  @foreach($subs as $s)
                  <tr class="border-b">
                    <td class="py-1 pr-2">{{ $s->student->name }}</td>
                    <td class="py-1 pr-2">{{ $s->status }}</td>
                    <td class="py-1 pr-2">{{ $s->grade ?? '-' }}</td>
                    <td class="py-1 pr-2">
                      @if($s->file_path)
                        <a class="text-blue-600 underline" href="{{ route('submissions.download',$s) }}">Download</a>
                      @else — @endif
                    </td>
                    @if(in_array($me->role,['admin','teacher']))
                    <td class="py-1 pr-2">
                      <form method="POST" action="{{ route('submissions.grade',$s) }}" class="flex gap-2">
                        @csrf
                        <input name="grade" value="{{ $s->grade }}" class="border p-1 rounded w-24" placeholder="A / 80%">
                        <select name="status" class="border p-1 rounded">
                          <option value="submitted" @selected($s->status==='submitted')>submitted</option>
                          <option value="graded" @selected($s->status==='graded')>graded</option>
                          <option value="late" @selected($s->status==='late')>late</option>
                        </select>
                        <button class="bg-gray-800 text-white px-2 rounded">Save</button>
                      </form>
                    </td>
                    @endif
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      @endforeach
    </div>

    <div>{{ $assignments->links() }}</div>
  </div>
</x-app-layout>
