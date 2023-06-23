<?php

    require_once 'config.php';

    $search_query = $_POST['search_query'];

    if (!empty($search_query)) {
        $sql = "SELECT * FROM users WHERE username LIKE '%$search_query%'";

        $result = $link->query($sql);

        if ($result->num_rows > 0) {
            $dropdown = '<ul id="search">';
            while($row = $result->fetch_assoc()) {
                $dropdown .= '<li><a href="pfviewer.php?user=' . $row["username"] . '"><img src="images/' . $row["username"] . '.jpg" alt="User" id="user" style="height: 50px">&nbsp<p style="">' . $row["username"] . '</p></a></li>';
            }
            $dropdown .= '</ul>';
        } else {
            $dropdown = '<ul id="search">
                            <li>No results found.</li>
                         </ul>';
        }
    } else {
        $dropdown = '';
    }

    $link->close();

    echo $dropdown;

?>