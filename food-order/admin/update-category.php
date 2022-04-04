<?php include('partials/menu.php');?>

<div class="main-content">
    <div class="wrapper">
        <h1>Update Category</h1>

        <br><br>

        <?php 

            //check wheather the id is set or not
            if(isset($_GET['id']))
            {
                //ge id and all other detailst
                //echo "getting the data";
                $id = $_GET['id'];
                //create SQL Query to get all other details
                $sql = "SELECT * FROM tbl_category WHERE id=$id";

                //Execute the Query
                $res = mysqli_query($conn, $sql);

                //count the rows to check wheather id is valid or not
                $count  = mysqli_num_rows($res);

                if($count==1)
                {
                    //get all data
                    $row = mysqli_fetch_assoc($res);
                    $title = $row['title'];
                    $current_image = $row['image_name'];
                    $featured = $row['featured'];
                    $active = $row['active']; 

                }
                else
                {
                    //redirect to manage category with session message
                    $_SESSION['no-category-found'] = "<div class = 'error'>Category Not Found</div>";
                    header('location:'.SITEURL.'admin/manage-category.php');
                }
            } 
            else 
            {
                //redirect to manage category
                header('location:'.SITEURL.'admin/manage-category.php');
            }

        ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <table class="tbl-30">
                <tr>
                    <td>Title: </td>
                    <td>
                        <input type="text" name="title" value="<?php echo $title;?>">
                    </td> 
                </tr>

                <tr>
                    <td>Current Image: </td>
                    <td>
                        <?php
                            if($current_image!='')
                            {
                                //display image
                                ?>
                                    <img src="<?php echo SITEURL; ?>images/category/<?php echo $current_image; ?>"width="150px";>
                                <?php
                            }
                            else
                            {
                                //display message
                                echo "<div class='error'>Image Not Added</div>";
                            }
                        ?>
                    </td>
                </tr>

                <tr>
                    <td>New Image: </td>
                    <td>
                        <input type="file" name="image" id="">
                    </td>
                </tr>

                <tr>
                    <td>Featured: </td>
                    <td>
                        <input <?php if($featured=="Yes"){echo "Checked";} ?> type="radio" name="featured" value="Yes"> Yes

                        <input <?php if($featured=="No"){echo "Checked";} ?> type="radio" name="featured" value="No"> No
                    </td>
                </tr>

                <tr>
                    <td>Active: </td>
                    <td>
                        <input <?php if($active=="Yes"){echo "checked";} ?> type="radio" name="active" value="Yes"> Yes

                        <input <?php if($active=="No"){echo "Checked";} ?> type="radio" name="active" value="No"> No
                    </td>
                </tr>

                <tr>
                    <td>
                        <input type="hidden" name="current_image" value="<?php echo $current_image; ?>">
                        <input type = 'hidden' name = "id" value="<?php echo $id; ?>">
                        <input type="submit" name="submit" value="Update Category" class="btn-secondary">
                    </td>
                </tr>
            </table>
        </form>

        <?php 
        
            if(isset($_POST['submit']))
            {
               // echo "clicked";
               //1.Get all the values from our form
                $id = $_POST['id'];
               $title = $_POST['title'];
               $current_image = $_POST['current_image'];
               $featured = $_POST['featured'];
               $active = $_POST['active'];

               //2.Updating new image if selected
               //check wheather image is selected or not
               if(isset($_FILES['image']['name']))
               {
                   //get image details
                   $image_name = $_FILES['image']['name'];

                   //check wheather image is available or not
                   if($image_name!="")
                   {
                       //image available
                       //A.upload new image

                         //Auto rename our image
                        //Get the extension of our image(jpg, png, gif) eg "food1.jpg"
                        $ext = end(explode('.', $image_name));

                        //rename image
                        $image_name = "food_category_".rand(000, 999).'.'.$ext;

                        $source_path = $_FILES['image']['tmp_name'];

                        $destination_path = "../images/category/".$image_name;

                        // Finally upload image
                        $upload = move_uploaded_file($source_path, $destination_path);

                        //check wheather image is uploaded or not
                        //if image is not uploaded then we will stop the process and redirect with error message
                        if($upload==false)
                        {
                            //set message
                            $_SESSION['upload'] = "<div class = 'error'>Failed To Upload Image</div>";
                            //Redirect to add category page
                            header('location:'.SITEURL.'admin/manage-category.php');
                            //stop the process
                            die();
                        }

                       //B.remove current image if available
                       if($current_image!="")
                       {
                            $remove_path = "../images/category/".$current_image;

                            $remove = unlink($remove_path);

                            //check wheather image is removed or not 
                            //if failed to remove display message and stop process
                            if($remove==false)
                            {
                                //failed to remove image
                                $_SESSION['failed-remove'] = "<div class='error'>Failed To Remove Current Image</div>";
                                header('location:'.SITEURL.'admin/manage-category.php');
                                die();//Stops the process
                            }
                        }
                   }
                   else
                    {
                        $image_name = $current_image;
                    }


               }
               else
               {
                   $image_name = $current_image;
               }

               //3.Update database

               $sql2 = "UPDATE tbl_category SET
               title = '$title',
               image_name = '$image_name',
               featured = '$featured',
               active = '$active'
               WHERE id = $id
               ";

               //execute the Query
               $res2 = mysqli_query($conn, $sql2);

               //4.Redirect to Manage Category With Message
               //Check wheather query executed or not
               if($res2==true)
               {
                   //category updated
                   $_SESSION['update'] = "<div class = 'success'> Category Updated Successfully</div>";
                   header('location: '.SITEURL.'admin/manage-category.php');
               }
               else
               {
                   //failed to update category
                   $_SESSION['update'] = "<div class = 'error'>Failed To Update Category</div>";
                   header('location: '.SITEURL.'admin/manage-category.php');
               }
               
            }
        
        ?>

    </div>
</div>


<?php include('partials/footer.php');?>