<!--  Kate McCarthy
      01/2021
      MedSched Page 2 (medication schedule)

      MedSched makes tracking the post-op medications of surgical patients a breeze.
      time.php contains the code that generates the schedule from the information entered into the form of index.php.
-->

<!DOCTYPE HTML>
<html>
<head>
<style>
</style>
<link rel="stylesheet" href="schedule.css" />
</head>
<body onload="onOpen()">

  <?php

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $dose = $_POST["dose"];
    $units = $_POST["units"];
    $hr = $_POST["hr"];
    $min = $_POST["min"];

    $info = $_POST["info"];

    $numDoses = $_POST["numDoses"];

    $numMeds = $_POST["numMeds"];

    function test_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }
  }
  ?>

<!-- MEDICATION SCHEDULE -->

<h2>Medication Schedule</h2>
<table id="medSched">
  <body>  <!-- list events chronologically -->

  </body>
</table>


<?php

// event color for each dose on the schedule
$colors = array();
$colorList = array("earth", "saturn", "mercury", "venus", "mars", "jupiter", "sun", "nebula");

// String with medication name, dose, and units for each dose
$eventTitles = array();

// hour in which each dose takes place
$hrs = array();
$hrIds = array();
$sortHrs = array();
$sortHrIds = array();

// the first hour of the day, at the latest
$firstHour = 8;

// minute in which each dose takes place
$mins = array();

// column width of each dose on the schedule
$colSpans = array();

for ($x = 0; $x <= $numMeds; $x++) {
  if ( !( empty($info[$x]) ) ) { // to account for any removed medications

    // assign the same color to all doses of each medication
    if ($x < count($colorList)) {
      $color = $colorList[$x];
    }
    else {
      $color = 'random';
      // assign random color in js
    }

    $doseLim = $info[$x]['numDoses'];
    for ($d = 0; $d < $doseLim; $d++) {
      $hrId = $x . 'hr' . $d;
      $minId = 'min' . $d;
      if ( empty($info[$x][$hrId]) ){
        if ( empty($info[$x][$minId]) ) {
          $doseLim = $doseLim + 1; // to account for any dose removals
        }
      } else {
        if ( empty($info[$x][$minId]) ) {
          $mins[] = '00'; // default minute value if only the hour was entered.
        } else {
          if ((int)$info[$x][$minId] < 10) {
            $mins[] = '0' . (int)$info[$x][$minId];
          } else {
            $mins[] = $info[$x][$minId];
          }
        }
        $hrs[] = $info[$x][$hrId];
        $hrIds[] = $hrId;
        $sortHrs[] = $info[$x][$hrId];
        $eventTitle = $info[$x]['name'] . ' (' . $info[$x]['dose'] . ' ' . $info[$x]['units'] . ')';
        $colors[] = $color;
        $eventTitles[] = $eventTitle;
      }
    }
  }
}
  // counts contains the number of instances of each element in hrs
  $counts = array_count_values($hrs);

  // sortHrs becomes a chronological version of hrs
  sort($sortHrs);

  // usedNums tracks which elements in sortHrs have already been paired with their ids
  $usedNums = array();

  for ($k = 0; $k < count($hrs); $k++) {
    // assign each dose the column width of 4 divided by the number of simultaneous doses
    $numMatch = $counts[$hrs[$k]];
    $colSpans[] = 4 / $numMatch;

    // set up a universal index by matching ids to sorted hrs
    for ($m = 0; $m < count($hrs); $m++) {
        if ($sortHrs[$k] == $hrs[$m]) {
          if (in_array($hrIds[$m], $usedNums)) {
          } else {
            // pairs the elements in sortHrs with their ids
            $sortHrIds[] = $hrIds[$m];
            $usedNums[] = $hrIds[$m];
          }
        }
    }
  }
  //print_r($sortHrIds);

  // firstHour is at least the hour of the earliest scheduled dose
  if ( !empty($sortHrs) ) $firstHour = $sortHrs[0];
  //print_r($firstHour);

?>


<script>


function onOpen() {

  var table = document.getElementById("medSched");


  if ( "<?php echo (!empty($info)); ?>") {

    // copy all arrays and variables into JavaScript from PHP
    var sortHrs = <?php echo '["' . implode('", "', $sortHrs) . '"]' ?>;
    var hrIds = <?php echo '["' . implode('", "', $hrIds) . '"]' ?>;
    var sortHrIds = <?php echo '["' . implode('", "', $sortHrIds) . '"]' ?>;
    var colors = <?php echo '["' . implode('", "', $colors) . '"]' ?>;
    var colSpans = <?php echo '["' . implode('", "', $colSpans) . '"]' ?>;
    var eventTitles = <?php echo '["' . implode('", "', $eventTitles) . '"]' ?>;
    var hrs = <?php echo '["' . implode('", "', $hrs) . '"]' ?>;
    var mins = <?php echo '["' . implode('", "', $mins) . '"]' ?>;

    // first hour label on schedule
    var minStart = 10;
    var startDay = "<?php echo $firstHour; ?>";
    if (startDay > minStart) startDay = minStart;

    // add each dose to the display in reverse order
    for (var h = sortHrs.length - 1; h >= 0; h--) {

      // match the index of the hour's id in sortHrIds to the index of the hour's id in hrIds
      var i = hrIds.indexOf(sortHrIds[h]);
      // the index of the target element in hrIds becomes the universal index used by all arrays
      var hr = hrs[i];
      var color = colors[i];
      var eventTitle = eventTitles[i];
      var colSpan = colSpans[i];

      // format the minute in which the scheduled dose takes place
      var min = parseInt((mins[i]).toString(), 10);
      if (min < 10) min = ':0' + min;
      else min = ':' + min;

      // determine the half of day in which the dose takes place
      var halfOfDay = "";
      var hr = parseInt(hr.toString(), 10); // h = hour of dose in 24-hour clock format
      if (hr == startDay && hr < 12) halfOfDay = "<b>a.m.</b>";
      else if (hr == 12 || (hr > 12 && hr == startDay)) halfOfDay = "<b>p.m.</b>";
      else halfOfDay = ":00";

      var ampm = "";
      if (hr < 12) ampm = "a.m.";
      else ampm = "p.m.";

      // convert hour from 24-hour clock format to 12-hour clock format
      var g = hr; // g = hour of dose in 12-hour clock format
      if (hr == 0) {
        g = 12;
      } else if (hr > 12) {
        g = hr - 12;
      }

      // if h is not the latest scheduled dose time
      if (h != sortHrs.length - 1) {
        // if this hour is not the same as the previous one
        if (sortHrs[h] != sortHrs[h+1])  {

          // insert new row into table with hour label
          var row = table.insertRow(0);
          row.id = hr.toString();
          var th = document.createElement('th');
          th.innerHTML = "" + g.toString() + halfOfDay;
          th.style.padding = "0.5rem 1rem";
          row.appendChild(th);

          // insert new cell to the right of the row with corresponding dose information, color, and width
          var cell = row.insertCell(1);
          cell.setAttribute('rowspan', '1');
          cell.setAttribute('class', ('stage-' + color));
          cell.setAttribute('colspan', colSpan);
          cell.innerHTML = eventTitle + '<br />' + g.toString() + min + ampm;

          // insert new row into table with half hour label
          var row1 = table.insertRow(1);
          var th1 = document.createElement('th');
          th1.style.padding = "0.5rem 1rem";
          th1.innerHTML = "" + g.toString() + ":30";
          row1.appendChild(th1);

          // if it is not the earliest hour
          if (h > 0) {

            // timeDif is the difference between this hour and the next hour to be added
            var timeDif = hr - parseInt(sortHrs[h-1].toString(), 10);

            // if there is a difference greater than one
            if (timeDif > 1) {

              //add in hour and hour:30 labels between the two doses
              for (var k = 1; k < timeDif; k++) {

                var hour = hr - k;

                if (hour == startDay && hour < 12) halfOfDay = "<b>a.m.</b>";
                else if (hour == 12 || (hour > 12 && hour == startDay)) halfOfDay = "<b>p.m.</b>";
                else halfOfDay = ":00";

                var clock = hour; // g = hour of dose in 12-hour clock format
                if (hour == 0) clock = 12;
                else if (hour > 12) clock = hour - 12;

                // insert new row into table with hour label
                var row = table.insertRow(0);
                row.id = hour.toString();
                var th = document.createElement('th');
                th.innerHTML = "" + clock.toString() + halfOfDay;
                th.style.padding = "0.5rem 1rem";
                row.appendChild(th);

                // insert new row into table with half hour label
                var row1 = table.insertRow(1);
                var th1 = document.createElement('th');
                th1.style.padding = "0.5rem 1rem";
                th1.innerHTML = "" + clock.toString() + ":30";
                row1.appendChild(th1);
              }
            }
          }
        } else { // if h is not the first instance of itself in the sortHrs array

          // insert new cell to the right of the appropriate label with corresponding dose information, color, and width
          var row = document.getElementById(hr.toString());
          var cell = row.insertCell(1);
          cell.setAttribute('rowspan', '1');
          cell.setAttribute('class', ('stage-' + color));
          cell.setAttribute('colspan', colSpan);
          cell.innerHTML = eventTitle + '<br />' + g.toString() + min + ampm;

        }
      } else { // if h is the latest scheduled dose time

        // insert new row into table with hour label
        var row = table.insertRow(0);
        row.id = hr.toString();
        var th = document.createElement('th');
        th.innerHTML = "" + g.toString() + halfOfDay;
        th.style.padding = "0.5rem 1rem";
        row.appendChild(th);

        // insert new cell to the right of the row with corresponding dose information, color, and width
        var cell = row.insertCell(1);
        cell.setAttribute('rowspan', '1');
        cell.setAttribute('class', ('stage-' + color));
        cell.setAttribute('colspan', colSpan);
        cell.innerHTML = eventTitle + '<br />' + g.toString() + min + ampm;

        // insert new row into table with half hour label
        var row1 = table.insertRow(1);
        var th1 = document.createElement('th');
        th1.style.padding = "0.5rem 1rem";
        th1.innerHTML = "" + g.toString() + ":30";
        row1.appendChild(th1);

        if (h > 0) { // if sortHrs has more than one element

          // timeDif is the difference between this hour and the next hour to be added
          var timeDif = hr - parseInt(sortHrs[h-1].toString(), 10);

          // if there is a difference greater than one
          if (timeDif > 1) {

            //add in hour and hour:30 labels between the two doses
            for (var k = 1; k < timeDif; k++) {

              // determine the half of day in which the dose takes place
              var hour = hr - k;

              if (hour == startDay && hour < 12) halfOfDay = "<b>a.m.</b>";
              else if (hour == 12 || (hour > 12 && hour == startDay)) halfOfDay = "<b>p.m.</b>";
              else halfOfDay = ":00";

              var clock = hour; // g = hour of dose in 12-hour clock format
              if (hour == 0) clock = 12;
              else if (hour > 12) clock = hour - 12;

              // insert new row into table with hour label
              var row = table.insertRow(0);
              row.id = hour.toString();
              var th = document.createElement('th');
              th.innerHTML = "" + clock.toString() + halfOfDay;
              th.style.padding = "0.5rem 1rem";
              row.appendChild(th);

              // insert new row into table with half hour label
              var row1 = table.insertRow(1);
              var th1 = document.createElement('th');
              th1.style.padding = "0.5rem 1rem";
              th1.innerHTML = "" + clock.toString() + ":30";
              row1.appendChild(th1);
            }
          }
        }

      }

      if (h == 0) { // if h is the index of the earliest dose

        // timeDif is the difference between this hour and the earliest hour on the schedule
        var timeDif = hr - startDay;

        // if there is a difference between this hour and the earliest hour on the schedule
        if (timeDif > 0) {

          //add in hour and hour:30 labels
          for (var k = 1; k < timeDif; k++) {

            // determine the half of day in which the dose takes place
            var hour = hr - k;

            if (hour == startDay && hour < 12) halfOfDay = "<b>a.m.</b>";
            else if (hour == 12 || (hour > 12 && hour == startDay)) halfOfDay = "<b>p.m.</b>";
            else halfOfDay = ":00";

            var clock = hour; // g = hour of dose in 12-hour clock format
            if (hour == 0) clock = 12;
            else if (hour > 12) clock = hour - 12;

            // insert new row into table with hour label
            var row = table.insertRow(0);
            row.id = hour.toString();
            var th = document.createElement('th');
            th.innerHTML = "" + clock.toString() + halfOfDay;
            th.style.padding = "0.5rem 1rem";
            row.appendChild(th);

            // insert new row into table with half hour label
            var row1 = table.insertRow(1);
            var th1 = document.createElement('th');
            th1.style.padding = "0.5rem 1rem";
            th1.innerHTML = "" + clock.toString() + ":30";
            row1.appendChild(th1);
          }
        }
      }
    }
 }
}
</script>

</body>
</html>
