<?php

/**
 * Created by PhpStorm.
 * User: Catherine
 * Date: 3/19/15
 * Time: 4:13 PM
 */

class ProfileController extends BaseController {

    private function imageTypeReconstructor($fileTypeList)
    {
        $fileTypeString = '';
        for($i = 0; $i < count($fileTypeList); $i++)
        {
            $fileTypeString = $fileTypeString.$fileTypeList[$i].'|';
           
        }
        
        return $fileTypeString;
    }
    
    public function updateProfile(){
        //Images
        $message = "File format is not supported.";
        $fileTypeList = "";
        $counter = 0;
        $fileTypeString = "";
        
        
        if(isset($_SESSION['fileTypeList']))
        {
            $fileTypeList = $_SESSION['fileTypeList'];
        }

        $email = Input::get('email');
        $user = User::where('email', '=', $email)->first();

        // Delete images
        if( Input::get('image1') !== null){
            $user->image1 = null;
            $user->number_images = $user->number_images - 1;
            $fileTypeList[0] = '';
        }
        if( Input::get('image2') !== null){
            $user->image2 = null;
            $user->number_images = $user->number_images - 1;
            $fileTypeList[1] = '';
        }
        if( Input::get('image3') !== null){
            $user->image3 = null;
            $user->number_images = $user->number_images - 1;
            $fileTypeList[2] = '';
        }
        if( Input::get('image4') !== null){
            $user->image4 = null;
            $user->number_images = $user->number_images - 1;
            $fileTypeList[3] = '';
        }

        $user->image_types = $this->imageTypeReconstructor($fileTypeList);
        $user->save();
        
        // Upload images
        if(isset($_FILES['image']) && $_FILES['image']['name'] != '')
        {
            
            $types = array('image/jpeg', 'image/gif', 'image/jpg', '', ' ');
            if(!in_array($_FILES['image']['type'], $types))
            {
                //return View::make('hello');
                //return var_dump($_FILES['image']['type']);
                return View::make('secureTest', array(
                    'user' => $user,
                    'message' => $message
                ));
            } 

            $fileTypeConstructorTrigger = false;

            
            for($i = 0; $i < count($fileTypeList); $i++)
            {
                if($fileTypeList[$i] == '' && $fileTypeConstructorTrigger == false)
                {
                    $imageData = file_get_contents(Input::file('image'));
                    $newFileType = str_replace('image/', '', $_FILES['image']['type']);
                    switch($i)
                    {
                        case 0:
                            $user->image1 = $imageData;
                            break;
                        case 1:
                            $user->image2 = $imageData;
                            break;
                        case 2:
                            $user->image3 = $imageData;
                            break;
                        case 3:
                            $user->image4 = $imageData;
                            break;
                    }
                    $fileTypeList[$i] = $newFileType;
                    $fileTypeConstructorTrigger = true;
                    $counter++;
                }
                
                
                else if($fileTypeList[$i] != '')
                {
                    $counter++;
                }
            }

        }


        $user->number_images = $counter;
        $user->image_types = $this->imageTypeReconstructor($fileTypeList);
        
        $_FILES['image'] = NULL;
        $notes = Input::get('notes');
        $user->notes = $notes;
        $user->to_do_list = Input::get('to-do-list');

        $links = "";

        for ($i = 0; $i < count($_POST['websites']); $i++)
        {
            if(isset($_POST['websites'][$i])&& $_POST['websites'][$i] !== ""){
                $links .= $_POST['websites'][$i] . "|";
            } elseif($_POST['websites'][$i] == ""){
                $links .= "";
            }
        }
        $user->href = $links;
        $user->save();
      
        
        return View::make('secureTest')->with('user', $user);
        //return var_dump($_FILES['image']['type']);
    }
}

