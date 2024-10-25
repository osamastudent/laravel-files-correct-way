
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{

    // login
    public function Login()
    {
        return view('login');
    }

    // register
    public function Register()
    {
        return view('register');
    }

    // user store with singlie

    // public function userStore(Request $request)
    // {
    //     $file = $request->file('image');
    //     $fileName = now()->timestamp . "rendom.png";
    //     $file->storeAs('images', $fileName, 'public');

    //     User::create([
    //         'name' => $request->name,
    //         'image' => $fileName,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);
    //     return "new record added.";
    // }

    // show
    public function show()
    {
        // $show = User::latest()->limit(3)->get();
        $show = User::paginate(2);
        return view('show', compact('show'));
    }


    // delete
    public function delete($id)
    {
        $delete = User::find($id);
        $path = 'images/' . $delete->image;
        if ($delete) {
            if (storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                $delete->delete();
            }
        } else {
            return "no record found.";
        }

        return back()->with('success', 'delete successfully.');
    }


    // delete all

    public function deleteAll()
    {
        $deleteAlls = User::all();
        if ($deleteAlls->count() > 0) {
            foreach ($deleteAlls as $deleteAll) {
                $filePath = 'images/' . $deleteAll->image;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                $deleteAll->delete();
            }
        } else {
            return back()->with('status', "No record found.");
        }

        return back()->with('status', "delete all records");
    }


    // user store with multiple files
    // public function userStore(Request $request)
    // {
    //     $files = $request->file('image');
    //     $multipleFiles = [];
    //     if ($files) {
    //         foreach ($files as $file) {
    //             $fileName = now()->timestamp . uniqid() . '.png';
    //             $file->storeAs('images', $fileName, 'public');
    //             $multipleFiles[] = $fileName;
    //         }
    //     }

    //     $implode = implode(',' , $multipleFiles);

    //     User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'image' => $implode,
    //         'password' => $request->password,
    //     ]);


    //     return back()->with('status', "multiple files added sucessfully.");
    // }


    // user store with multiple fields

    // public function userStore(Request $request)
    // {

    //     $fields = ['image', 'imagetwo'];
    //     $multipleFiles = [];
    //     foreach ($fields as $field) {
    //         $files = $request->file($field);
    //         if ($files) {
    //             $fileName = now()->timestamp . uniqid() . '.png';
    //             $files->storeAs('images', $fileName, 'public');
    //             $multipleFiles[$field] = $fileName;
    //         }
    //     }

    //     // dd($multipleFiles[$field]);
    //     User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'image' => $multipleFiles['image'],
    //         'imagetwo' => $multipleFiles['imagetwo'],
    //         'password' => $request->password,
    //     ]);


    //     return back()->with('status', "multiple files added sucessfully.");
    // }



    // user store with multiple fields and files

    public function userStore(Request $request)
    {
        $fields = ['image', 'imagetwo'];
        $multipleFiles = [];
        foreach ($fields as $field) {
            $files = $request->file($field);
            if ($files) {
                foreach ($files as $file) {
                    $fileName = now()->timestamp . uniqid() . '.png';
                    $file->storeAs('images', $fileName, 'public');
                    $multipleFiles[$field][] = $fileName;
                }
            }
        }

        $implodeImage = implode(',', $multipleFiles['image']);
        $implodeImageTwo = implode(',', $multipleFiles['imagetwo']);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'image' => $implodeImage,
            'imagetwo' => $implodeImageTwo,
            'password' => $request->password,
        ]);

        return back()->with('status', "multiple files added sucessfully.");
    }


    // user store with 1 field is multiple and 1 is single file

    // public function userStore(Request $request)
    // {
    //     $fields = ['image', 'imagetwo'];
    //     $multipleFiles = [];

    //     foreach ($fields as $field) {
    //         $files = $request->file($field);

    //         if ($files) {
    //             if (is_array($files)) {
    //                 foreach ($files as $file) {
    //                     $fileName = now()->timestamp . uniqid() . '.png';
    //                     $file->storeAs('images', $fileName, 'public');
    //                     $multipleFiles[$field][] = $fileName;
    //                 }
    //             } else {
    //                 $fileName = now()->timestamp . uniqid() . '.png';
    //                 $files->storeAs('images', $fileName, 'public');
    //                 $multipleFiles[$field][] = $fileName;
    //             }
    //         }
    //     }

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'image' => implode(',', $multipleFiles['image'] ?? []),
    //         'imagetwo' => $multipleFiles['imagetwo'][0] ?? null,
    //         'password' => $request->password,
    //     ]);

    //     return back()->with('status', "Multiple files added successfully.");
    // }


    // delete multiple files
    // public function deleteMultiple($id)
    // {
    //     $delete = User::find($id);

    //     if (!$delete) {
    //         return back()->with('error', 'No record found.');
    //     }
    //     $explodeImages = explode(",", $delete->image);
    //     $explodeImagesTwo = explode(",", $delete->imagetwo);
    //     foreach ($explodeImages as $old) {
    //         if (Storage::disk('public')->exists('images/' . $old)) {
    //             Storage::disk('public')->delete('images/' . $old);
    //         }
    //                 }
    //     foreach ($explodeImagesTwo as $explodeImages) {
    //         if (Storage::disk('public')->exists('images/' . $explodeImages)) {
    //             Storage::disk('public')->delete('images/' . $explodeImages);
    //         }
    //     }
    //     if ($delete->delete()) {
    //         return back()->with('success', 'Deleted successfully.');
    //     } else {
    //         return back()->with('error', 'Delete operation failed.');
    //     }
    // }


    // delete single single file
    public function singleslinglefile($id)
    {
        $delete = User::find($id);

        if (!$delete) {
            return back()->with('status', 'No record found.');
        }
        $file1Path = 'images/' . $delete->image;
        $file2Path = 'images/' . $delete->imagetwo;
        if (Storage::disk('public')->exists($file1Path) && Storage::disk('public')->exists($file1Path)) {
            Storage::disk('public')->delete($file1Path);
            Storage::disk('public')->delete($file2Path);
        }
        $delete->delete();

        return back()->with('success', 'Deleted successfully.');
    }


    // edit
    public function edit($id)
    {
        $edit = User::find($id);
        return view('edit', compact('edit'));
    }

    // update single sigle file
    // public function update(Request $request, $id)
    // {
    //     $edit = User::find($id);
    //     if (!$edit) {
    //         return back()->with('error', 'No record found.');
    //     }

    //     $file1 = $request->file('image');
    //     $file2 = $request->file('imagetwo');

    //     if ($file1) {
    //         $file1Name = now()->timestamp . uniqid()  . '.png';
    //         $file1->storeAs('images', $file1Name, 'public');
    //         $image1 = 'images/' . $edit->image;
    //         if (Storage::disk('public')->exists($image1)) {
    //             Storage::disk('public')->delete($image1);
    //         }
    //     }


    //     if ($file2) {         
    //         $file2Name = now()->timestamp . uniqid()  . '.png';
    //         $file2->storeAs('images', $file2Name, 'public');
    //         $image2 = 'images/' . $edit->imagetwo;
    //         if (Storage::disk('public')->exists($image2)) {
    //             Storage::disk('public')->delete($image2);
    //         }           
    //     }

    //     $oldImage1=$edit->image;
    //     $oldImage2=$edit->imagetwo;

    //     $edit->name=$request->name;
    //     $edit->email=$request->email;
    //     $edit->password=Hash::make($request->password);
    //     $edit->image=$request->image ? $file1Name : $oldImage1;
    //     $edit->imagetwo=$request->imagetwo ? $file2Name : $oldImage2;
    //     $edit->save();

    //     return redirect()->route('show')->with('status','record updated successfully.');

    // }


    // update multiple files
    public function update(Request $request, $id)
    {
        $edit = User::find($id);
        $fields = ['images', 'imagetwo'];
        $multipleFiles = [];


        $file1 = $request->file('image');
        $file2 = $request->file('imagetwo');

        if ($file1) {
            foreach ($file1 as $file) {
                $fileName1 = now()->timestamp . uniqid() . '.png';
                $file->storeAs('images', $fileName1, 'public');
                $multipleFiles[] = $fileName1;
            }
            $oldimages1 = explode(",", $edit->image);
            foreach ($oldimages1 as $oldimage) {
                if (Storage::disk('public')->exists('images/' . $oldimage)) {
                    Storage::disk('public')->delete('images/' . $oldimage);
                }
            }
          
           
        }

        if ($file2) {
            foreach ($file2 as $file) {
                $fileName2 = now()->timestamp . uniqid() . '.png';
                $file->storeAs('images', $fileName2, 'public');
                $multipleFiles[] = $fileName2;
            }
            $oldimages2 = explode(",", $edit->imagetwo);
            foreach ($oldimages2 as $oldimage2) {
                if (Storage::disk('public')->exists('images/' . $oldimage2)) {
                    Storage::disk('public')->delete('images/' . $oldimage2);
                }
            }
          
        }


        $oldImage1 = $edit->image;
        $oldImage2 = $edit->imagetwo;
        
        $implodeImage=implode(",",$multipleFiles);
        $implodeImageTwo=implode(",",$multipleFiles);

        $edit->name = $request->name;
        $edit->email = $request->email;
        $edit->password = Hash::make($request->password);
        $edit->image = $request->image ? $implodeImage : $oldImage1;
        $edit->imagetwo = $request->imagetwo ? $implodeImageTwo : $oldImage2;
        $edit->save();
        return redirect()->route('show')->with('status','record updated successfully.');

    }
}
