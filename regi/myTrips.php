<!--

    AMC Trip Registration System
    Copyright (C) 2010 Dirk Koechner

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, version 3 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    For a copy of the GNU General Public License, please refer to
    <http://www.gnu.org/licenses/>.

-->



<?php


    include 'utils.php';
    session_start();
    UTILdbconnect();
    CHUNKgivehead();
    CHUNKstartbody();
    UTILbuildmenu();
    if (isset($_SESSION['Smessage']))
        print "<b><font color='red'>$_SESSION[Smessage]</font></b>";

?>

    </div>
   </div>
   <div style="padding-left:20px; width:90%;">

<h1>My Trips</h1>

<?php

    if (!isset($_SESSION['Suser_id']))
    {
        // ERROR?

        $my_user_id='';
        $my_user_type='';

        header("Location: ./errorPage.php?errTitle=Authorization Error&errMsg=User Not Authorized to View this Profile");
        exit();
    }
    else
    {
        $my_user_id=$_SESSION['Suser_id'];
        $my_user_type=$_SESSION['Suser_type'];

        // Show all events I am connected with as: signed up/wait list, selected
        //   leader, or co-leader
        //

        $query = "select events.event_id, event_name, event_status, register_date,
                register_status, program_id, start_date
                FROM events, user_events
                WHERE events.event_id=user_events.event_id
                AND user_events.user_id=$my_user_id
                ORDER BY start_date DESC
                LIMIT 30;";

        $result = mysql_query($query);
        if (!$result) UTILdberror($query);

        $numrows = mysql_num_rows($result);
        if ($numrows < 1 && $my_user_type == 'USER')
        {
            print " <h3>Welcome!</h3>
                <h3>You have not yet signed up for any events.</h3>
                <h3>Please view trip listings and click the 'Register Online' link for the trip you are interested in.</h3>";
        }
        else if ($numrows < 1 && $my_user_type == 'LEADER')
        {
            print " <h3>Welcome AMC Leader!</h3>
                <h3>You have not created any trips on this registration system yet.</h3>
                <h3>Please click the 'New Event' menu option above to enter in your first trip.</h3>";
        }
        else
        {

            print"<table><tr><td>Event</td><td>Date</td><td>Role</td><td>Status</td></tr>";
            while($row = mysql_fetch_assoc($result)) {
                print IN1()."<div class='listing'><tr>";
                print IN2()."<td><strong><a href=\"eventRegistration.php?event_id=$row[event_id]\" >$row[event_name]</a></strong><br>";



                print IN2()."<td>".UTILtime($row['start_date'])."</td>";
                print IN2()."<td>".ucfirst(strtolower($row[register_status]))."</td>";
                print IN2()."<td>".ucfirst(strtolower($row[event_status]))."</td>";
                if ($row['register_status']=='LEADER' || $row['register_status']=='CO-LEADER'
                    || $row['register_status']=='REGISTRAR') {
                    print IN2()."<td><a href=\"eventAdmin.php?event_id=$row[event_id]\" >Manage</a></td>";
                }
                print IN1()."</tr></div>";
            }
            print "</table>";
        }

    }
?>

<br><br>
</div>
</body>
</html>
