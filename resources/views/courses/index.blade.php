<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl leading-tight">Courses</h2>
  </x-slot>

  <div class="p-6 space-y-8">
    @if (session('ok'))
      <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('ok') }}</div>
    @endif

    {{-- Create Course (admin/teacher only) --}}
    @if (in_array(auth()->user()->role, ['admin','teacher']))
      <div class="bg-white shadow rounded p-4">
        <h3 class="font-semibold mb-3">Create Course</h3>

        <form method="POST" action="{{ route('courses.store') }}" class="grid gap-3">
          @csrf

          <input class="border p-2 rounded" name="code" placeholder="CS101" required>
          <input class="border p-2 rounded" name="title" placeholder="Course title" required>
          <textarea class="border p-2 rounded" name="description" placeholder="Description"></textarea>

          {{-- Admin can choose a teacher id; teachers will be set automatically in controller --}}
          @if(auth()->user()->role==='admin')
            <input class="border p-2 rounded" name="teacher_id" placeholder="Teacher ID (optional)">
          @endif

          <div class="grid md:grid-cols-2 gap-3">
            <input type="date" class="border p-2 rounded" name="start_date">
            <input type="date" class="border p-2 rounded" name="end_date">
          </div>

          <input type="number" min="1" class="border p-2 rounded" name="capacity" placeholder="Capacity (e.g., 30)">

          <select class="border p-2 rounded" name="status">
            <option value="draft">Draft</option>
            <option value="active" selected>Active</option>
            <option value="completed">Completed</option>
            <option value="archived">Archived</option>
          </select>

          {{-- The important part: explicit submit button --}}
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded self-start">
            Create Course
          </button>
        </form>
      </div>
    @endif

    {{-- List courses --}}
    <div class="space-y-4">
      @forelse($courses as $course)
        <div class="bg-white shadow rounded p-4">
          <div class="text-sm text-gray-500">{{ $course->code }}</div>
          <h3 class="font-semibold text-lg">{{ $course->title }}</h3>
          <p class="mt-1 text-gray-700">{{ \Illuminate\Support\Str::limit($course->description, 140) }}</p>

          <div class="mt-3 flex items-center gap-3">
            <a class="text-blue-600 underline" href="{{ route('assignments.index', $course) }}">
              Assignments
            </a>
            <a class="text-blue-600 underline" href="{{ route('attendance.sessions',$course) }}">Attendance</a>


            @if(auth()->user()->role==='student' && $course->status==='active')
              <form method="POST" action="{{ route('courses.enroll', $course) }}">
                @csrf
                <button class="bg-emerald-600 text-white px-3 py-1 rounded">Enroll</button>
              </form>
            @endif
          </div>
        </div>
      @empty
        <p>No courses yet.</p>
      @endforelse

      {{-- pagination --}}
      <div>{{ $courses->links() }}</div>
    </div>
  </div>
</x-app-layout>
