<?php
@session_start();
include "../../connection/dbconnect.php";
include "../../Auth/auth.php";
//include $_SERVER['DOCUMENT_ROOT']."/LibraryManagement/Auth/auth.php";
if(!verification() || $_POST["Access"] != "Main-Delete" )
{
    header("Location: /LibraryManagement/");
}
else
{
    $flag=0;
    $bookExist = false;
    $bookno=$_POST["bookno"];
    $bookcheck="SELECT Book_No from books where Book_No = '$bookno';";
    $sqlcheck="SELECT Issue_Bookno, Return_Date,Issue_By from issue_return where Issue_Bookno = '$bookno';";
    $bookresultcheck= $conn->query($bookcheck);
    $resultcheck=$conn->query($sqlcheck);
    if($resultcheck && $bookresultcheck)
    {
        if(mysqli_num_rows($bookresultcheck)>=1) $bookExist=true;
        if($bookExist)
        {
            while($row=$resultcheck->fetch_assoc())
            {
                if($row["Issue_Bookno"] == $bookno && $row["Return_Date"] == null)
                {
                    $flag=1;
                    echo "
                    <div id='dialog-confirm' style='color:red;' title='Notification ❌'>
                        <p class='notification-message'>Book $bookno is been issued by ".$row["Issue_By"]." so it cannot be deleted!!!</p>
                    </div>
                    "; 
                    echo"<script>
                    $( function() {
                    $( '#dialog-confirm' ).dialog({
                        resizable: false,
                        height: 'auto',
                        width: 400,
                        modal: true,
                        buttons: {
                        'Ok': function() {
                            $( this ).dialog( 'close' );
                        }
                        }
                    });
                    } );
                    </script>";
                }
            }
        }
    }
    else echo $conn->error;
    if($flag==0 && $bookExist)
    {
        echo "<div id='dialog-confirm' title='Delete Book ⚠️'>
                    <p class='notification-message'> Book $bookno will be permanently deleted and cannot be recovered. Are you sure?</p>
                    </div>";
                echo"<script>
                $( function() {
                  $( '#dialog-confirm' ).dialog({
                    resizable: false,
                    height: 'auto',
                    width: 400,
                    modal: true,
                    buttons: {
                      'Delete Book': function() {
                        $.ajax(
                            {
                                method: 'post',
                                url: './Books/Transactions/Book_delete.php',
                                data: $(this).serialize() + '&Access=' +'Delete-DSucc&' +'&bookno=' +'$bookno',
                                datatype: 'text',
                                error: function()
                                {
                                    alert('Some Error Occurred!!!');
                                },
                                success: function(Result)
                                {
                                    $( '#dialog4' ).dialog( 'destroy' );
                                    $('#response4').html(Result);
                                    $('#dialog4').dialog();
                                }
                            });
                        $( this ).dialog( 'close' );

                      },
                      'Cancel': function() {
                        $( this ).dialog( 'close' );
                      }
                    }
                  });
                } );
                </script>";
    }
    else if(!$bookExist)
    {
        echo "
        <div id='dialog-confirm' style='color:red;' title='Notification ❌'>
            <p class='notification-message'>Book $bookno does not Exist</p>
        </div>
        ";
        echo"<script>
        $( function() {
        $( '#dialog-confirm' ).dialog({
            resizable: false,
            height: 'auto',
            width: 400,
            modal: true,
            buttons: {
            'Ok': function() {
                $( this ).dialog( 'close' );
            }
            }
        });
        } );
        </script>"; 
    }
}
