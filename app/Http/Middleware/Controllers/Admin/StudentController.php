<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\School;
use App\Models\StudentDocument;
use App\Models\StudentSchool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as Image;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DB;
use Excel;
use Validator;
use Auth;
use File;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.student.index');
    }

    /**
     * Get data students.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $students = DB::table('students')
                ->select([
                  DB::raw('@rownum := @rownum + 1 AS rownum'),
                  'id',
                  'nisn',
                  'first_name',
                  'last_name',
                  'photo',
                  'birth_date'
                ]);

        $datatables = Datatables::of($students)
          ->addColumn('action', function ($students) {
              $act = '<a href="'. route("admin.student.show", ['student' => $students->id]) .'"><i class="icon-magnifier"></i></a>';
              $act .= ' <a href="'. route("admin.student.edit", ['student' => $students->id]) .'"><i class="icon-pencil"></i></a>';
              $act .= ' <a><i data-id="'. $students->id .'" class="delete icon-trash"></i></a>';

              return $act;
          })
          ->removeColumn('id')
          ->editColumn('photo', function ($data) {
            $image = '<center><img width="150" src="'. asset('/assets/images/avatar.jpeg') .'" /></center>';
            if (!empty($data->photo)) {
                $image = '<center><a target="_blank" href="'. asset('/uploads/photo/' . $data->photo). '"><img width="150" src="'. asset('/uploads/photo/200_200_' . $data->photo) .'" /></a></center>';
            }

            return $image;
          })
          ->rawColumns(['photo', 'action']);

        if ($keyword = $request->get('search')['value']) {
            $datatables->filterColumn('rownum', function($query, $keyword) {
                    $sql = '@rownum + 1 like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
            });
        }

        return $datatables->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $schools = School::pluck('name', 'code');
        return view('backend.student.form', compact('schools'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'first_name' => 'required',
          'last_name' => 'required',
          'nisn' => 'required|unique:students',
          'photo' => 'required',
          'gender' => 'required',
          'birth_date' => 'required',
          'slh_location' => 'required',
          'academy' => 'required',
          'grade' => 'required',
          'parent_b' => 'required',
          'dream' => 'required',
          'school_code' => 'required',
          'is_sponsored' => 'required',
          'parent_background_en' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        DB::beginTransaction();
        try {
          $student = new Student;
          $student->first_name = $request->first_name;
          $student->last_name = $request->last_name;
          $student->nisn = $request->nisn;
          $student->gender = $request->gender;
          $student->birth_date = $request->birth_date;
          $student->slh_location = $request->slh_location;
          $student->academy = $request->academy;
          $student->grade = $request->grade;
          $student->parent_b = $request->parent_b;
          $student->dream = $request->dream;
          $student->school_code = $request->school_code;
          $student->is_sponsored = $request->is_sponsored;
          $student->parent_background_en = $request->parent_background_en;
          $student->parent_background_id = $request->parent_background_id;
          $student->updated_at = date('Y-m-d H:i:s');

          if($request->hasFile('photo') && $request->file('photo')->isValid()){
            $file = $request->file('photo');
            $fileName = 'student-'. str_slug($request->first_name, '-') . '-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();

            $thumbnailPath = public_path('uploads/photo/200_200_' . $fileName);
            Image::make($file->getRealPath())->fit(200, 200)->save($thumbnailPath);

            $originalPath = public_path('uploads/photo/' . $fileName);
            Image::make($file->getRealPath())->save($originalPath);

            $student->photo = $fileName;
          }

          $student->save();
          $this->storeSchools($request, $student);
          $this->storeDocuments($student);

          DB::commit();
          return redirect()->route('admin.student.index')->with('status', 'Data saved successfully!');
        } catch (\Exception $e) {
          DB::rollBack();
          return redirect()->back()->withInput()->withErrors([$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Student::where('id', $id)->firstOrFail();
        return view('backend.student.detail', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Student::where('id', $id)->firstOrFail();
        $schools = School::pluck('name', 'code');
        return view('backend.student.form', compact('data', 'schools'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $validator = Validator::make($request->all(), [
        'first_name' => 'required',
        'last_name' => 'required',
        'nisn' => 'required|unique:students,nisn,' . $id,
        'gender' => 'required',
        'birth_date' => 'required',
        'slh_location' => 'required',
        'academy' => 'required',
        'grade' => 'required',
        'parent_b' => 'required',
        'dream' => 'required',
        'school_code' => 'required',
        'is_sponsored' => 'required',
        'parent_background_en' => 'required',
      ]);

      if ($validator->fails()) {
          return redirect()->back()->withInput()->withErrors($validator);
      }

      try {
        $student = Student::find($id);
        $student->first_name = $request->first_name;
        $student->last_name = $request->last_name;
        $student->nisn = $request->nisn;
        $student->gender = $request->gender;
        $student->birth_date = $request->birth_date;
        $student->slh_location = $request->slh_location;
        $student->academy = $request->academy;
        $student->grade = $request->grade;
        $student->parent_b = $request->parent_b;
        $student->dream = $request->dream;
        $student->school_code = $request->school_code;
        $student->is_sponsored = $request->is_sponsored;
        $student->parent_background_en = $request->parent_background_en;
        $student->parent_background_id = $request->parent_background_id;
        $student->updated_at = date('Y-m-d H:i:s');

        if ($request->hasFile('photo') && $request->file('photo')->isValid()){
            $oldPhotoPath = public_path('uploads/photo/' . $student->photo);
            $oldThumbnailPhotoPath = public_path('uploads/photo/' . $student->photo);
            
            $file = $request->file('photo');
            $fileName = 'student-'. str_slug($request->first_name, '-') . '-' . date('YmdHis') . '.' . $file->getClientOriginalExtension();

            $thumbnailPath = public_path('uploads/photo/' . $fileName);
            Image::make($file->getRealPath())->fit(200, 200)->save($thumbnailPath);

            $originalPath = public_path('uploads/photo/' . $fileName);
            Image::make($file->getRealPath())->save($originalPath);

            $student->photo = $fileName;
        }

        $student->save();

        if (isset($oldPhotoPath) && File::exists($oldPhotoPath)) {
            File::delete($oldPhotoPath);
            File::delete($oldThumbnailPhotoPath);
        }

        $this->updateDocuments($request, $student);
        $this->updateSchools($student);

        return redirect()->route('admin.student.index')->with('status', 'Data updated successfully!');
      } catch (\Exception $e) {
        return redirect()->back()->withInput()->withErrors([$e->getMessage()]);
      }
    }

    /**
     * Update student documents.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDocuments(Request $request, $student)
    {
        // Check if selected year documents already exist
        $document = StudentDocument::where('student_id', $student->id)->where('period', $request->period)->first();
        if (empty($document)) {
            $this->storeDocuments($request, $student);
        } else {
            if ($request->hasFile('file_1') || $request->hasFile('file_2') || $request->hasFile('file_3')) {
                if ($request->hasFile('file_1')) {
                  $file = $request->file('file_1');
                  $fileNameFile1 = 'docs_1-'. str_slug($request->first_name, '-') . '-' . $request->period . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                  $file->move(public_path() . '/uploads/documents', $fileNameFile1);
                    
                  $oldFile1 = public_path('uploads/documents/' . $document->file_1);
                  $document->file_1 = $fileNameFile1;
                }
        
                if ($request->hasFile('file_2')) {
                  $file = $request->file('file_2');
                  $fileNameFile2 = 'docs_2-'. str_slug($request->first_name, '-') . '-' . $request->period . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                  $file->move(public_path() . '/uploads/documents', $fileNameFile2);
                    
                  $oldFile2 = public_path('uploads/documents/' . $document->file_2);
                  $document->file_2 = $fileNameFile2;
                }
        
                if ($request->hasFile('file_3')) {
                  $file = $request->file('file_3');
                  $fileNameFile3 = 'docs_3-'. str_slug($request->first_name, '-') . '-' . $request->period . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                  $file->move(public_path() . '/uploads/documents', $fileNameFile3);
        
                  $oldFile3 = public_path('uploads/documents/' . $document->file_3);
                  $document->file_3 = $fileNameFile3;
                }
        
                $document->save();

                if (isset($oldFile1) && File::exists($oldFile1)) {
                    File::delete($oldFile1);
                }

                if (isset($oldFile2) && File::exists($oldFile2)) {
                    File::delete($oldFile2);
                }

                if (isset($oldFile3) && File::exists($oldFile3)) {
                    File::delete($oldFile3);
                }
            }
        }
    }

    /**
     * Insert student documents.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeDocuments(Request $request, $student)
    {
        // If upload documents
        $documents = [];
        if ($request->hasFile('file_1')) {
          $file = $request->file('file_1');
          $fileNameFile1 = 'docs_1-'. str_slug($request->first_name, '-') . '-' . $request->period . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
          $file->move(public_path() . '/uploads/documents', $fileNameFile1);

          $documents['file_1'] = $fileNameFile1;
        }

        if ($request->hasFile('file_2')) {
          $file = $request->file('file_2');
          $fileNameFile2 = 'docs_2-'. str_slug($request->first_name, '-') . '-' . $request->period . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
          $file->move(public_path() . '/uploads/documents', $fileNameFile2);

          $documents['file_2'] = $fileNameFile2;
        }

        if ($request->hasFile('file_3')) {
          $file = $request->file('file_3');
          $fileNameFile3 = 'docs_3-'. str_slug($request->first_name, '-') . '-' . $request->period . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
          $file->move(public_path() . '/uploads/documents', $fileNameFile3);

          $documents['file_3'] = $fileNameFile3;
        }

        if (!empty($documents)) {
          $documents['period'] = $request->period;
          $student->documents()->save(new StudentDocument($documents));
        }
    }

    /**
     * Insert student schools.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeSchools($student)
    {
        $studentSchool = new StudentSchool;
        $studentSchool->student_id = $student->id;
        $studentSchool->school_id = $student->school->id;
        $studentSchool->period = date('Y');
        $studentSchool->save();
    }


     /**
     * Update student schools.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSchools($student)
    {
        $studentSchool = StudentSchool::where('student_id', $student->id)->where('period', date('Y'))->first();
        if (empty($studentSchool)) {
            $this->storeSchools($student);
        } else {
            $studentSchool->school_id = $student->school->id;
            $studentSchool->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
          $student = Student::where('id', $id)->firstOrFail();
          $photoPath = public_path('uploads/photo/' . $student->photo);
          $thumbnailPhotoPath = public_path('uploads/photo/200_200_' . $student->photo);

          if ($student->delete()) {
            if (File::exists($photoPath)) {
                File::delete($photoPath);
                File::delete($thumbnailPhotoPath);
            }
          }

          return response()->json([]);
        } catch (\Exception $e) {
          return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Upload data students.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
      $validator = Validator::make($request->all(), [
          'file' => 'required'
      ]);

      // process the form
      if ($validator->fails()) {
          return redirect()->back()->withErrors($validator);
      }

      if($request->file('file'))
      {
          DB::beginTransaction();
          try {
            $path = $request->file('file')->getRealPath();
            $data = Excel::load($path, function($reader) {})->get();
            $headerRow = $data->first()->keys()->toArray();

            $formatUpload = [
                'first_name' => 0,
                'last_name' => 1,
                'gender' => 2,
                'birth_date' => 3,
                'nisn' => 4,
                'slh_location' => 5,
                'academy' => 6,
                'grade' => 7,
                'parent_b' => 8,
                'dream' => 9,
                'parent_background_en' => 10,
                'parent_background_id' => 11
            ];

            foreach ($data as $k => $v) {
                if (empty($v[$headerRow[$formatUpload['first_name']]])) {
                    continue;
                }
                $student = new Student;
                foreach ($formatUpload as $kdx => $idx) {
                    if (isset($v[$headerRow[$idx]]) && !empty($v[$headerRow[$idx]])) {
                        if ($kdx === 'gender') {
                            $v[$headerRow[$idx]] = ($v[$headerRow[$idx]] === 'Male') ? 'M' : 'F';
                        }

                        $student->{$kdx} = $v[$headerRow[$idx]];
                    }
                }

                $student->updated_at = date('Y-m-d H:i:s');
                $student->save();
            }

            DB::commit();
            return response()->json([]);
          } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
          }
      }
    }

    /**
     * Download data students.
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $data = Student::select([
            'first_name',
            'last_name',
            'gender',
            'birth_date',
            'nisn',
            'slh_location',
            'academy',
            'grade',
            'parent_b',
            'dream',
            'parent_background_en',
            'parent_background_id'
        ])->get();

        Excel::create('Student', function($excel) use($data) {
            // Set the title
            $excel->setTitle('Student Data');
            // Call them separately
            $excel->setDescription('A student file');

            $excel->sheet('DATABASE', function ($sheet) use($data) {
                $sheet->cell('A1', function($cell) { $cell->setValue('First Name'); });
                $sheet->cell('B1', function($cell) { $cell->setValue('Last Name'); });
                $sheet->cell('C1', function($cell) { $cell->setValue('Gender'); });
                $sheet->cell('D1', function($cell) { $cell->setValue('Birth Date'); });
                $sheet->cell('E1', function($cell) { $cell->setValue('NISN'); });
                $sheet->cell('F1', function($cell) { $cell->setValue('Slh Loc'); });
                $sheet->cell('G1', function($cell) { $cell->setValue('Academy'); });
                $sheet->cell('H1', function($cell) { $cell->setValue('Grade'); });
                $sheet->cell('I1', function($cell) { $cell->setValue('Parent B'); });
                $sheet->cell('J1', function($cell) { $cell->setValue('Dream'); });
                $sheet->cell('K1', function($cell) { $cell->setValue('Parent Background En'); });
                $sheet->cell('L1', function($cell) { $cell->setValue('Parent Background Id'); });
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $i = $key + 2;
                        $sheet->cell('A' . $i, $value['first_name']); 
                        $sheet->cell('B' . $i, $value['last_name']); 
                        $sheet->cell('C' . $i, ($value['gender'] === 'M' ? 'Male' : 'Female'));
                        $sheet->cell('D' . $i, $value['birth_date']); 
                        $sheet->cell('E' . $i, $value['nisn']); 
                        $sheet->cell('F' . $i, $value['slh_location']); 
                        $sheet->cell('G' . $i, $value['academy']); 
                        $sheet->cell('H' . $i, $value['grade']); 
                        $sheet->cell('I' . $i, $value['parent_b']); 
                        $sheet->cell('J' . $i, $value['dream']); 
                        $sheet->cell('K' . $i, $value['parent_background_en']); 
                        $sheet->cell('L' . $i, $value['parent_background_id']); 
                    }
                }
            });
        })->download('xlsx');
    }

}
