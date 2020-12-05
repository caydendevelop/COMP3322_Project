<?php session_start(); 
// $_SESSION['Space'] = "original";
?>
<!--上方語法為啟用session，此語法要放在網頁最前方-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<html>
<head>
  <title>MainPage</title>
  <link rel="stylesheet" href="../css/MainPage1.css">
</head>
<body>
  <nav class="navBar">
    <a href='./MainPage.php'><button class="topNavButton">Ques</button></a>
    <a href='./MainPage.php'><button class="topNavButton">Home</button></a>
    <!-- <input class="topNavButton" type="submit" name="submitHot" id="buttonHot" value="Hot" onclick="hotFilter()"/> -->
    <button class="topNavButton" onclick="hot()">Hot1</button>
    <input type="text" class="navSearch" name="submitSearch" id="navSearch" onkeyup="navSearch(this.value)"/>
    <?php
      if($_SESSION['user_logged_in'] === true) {
        echo "<a href='./LogoutFunction.php'> <button class='topNavButton navAlignRight'>Log out</button></a>";
        //echo'<input type="submit" name="submit" class="topNavButton navAlignRight" id="LogoutButton" value="Log Out" action="./LogoutFunction.php"/>';
      }else{
        echo "<a href='./RegisterPage.php'><button class='topNavButton navAlignRight'>Register</button></a>
              <a href='./LoginPage.php'><button class='topNavButton navAlignRight'>Log in</button></a>";
      }
    ?>

  </nav>

  <div class="leftNav">
 
    <input class="leftNavButton" type="submit" name="submitAlgorithm" id="buttonAlgorithm" value="Algorithm" onclick="algorithmFilter()"/>
    <input class="leftNavButton" type="submit" name="submitML" id="buttonML" value="Machine Learning" onclick="MLFilter()"/>
    <input class="leftNavButton" type="submit" name="submitSystem" id="buttonSystem" value="System"  onclick="SystemFilter()"/>
    <input class="leftNavButton" type="submit" name="submitJavascript" id="buttonJavascript" value="Javascript"  onclick="JavascriptFilter()"/>
    
  </div>

  <main class="mainContainer">

    <?php

      if($_SESSION['user_logged_in'] === true) {
        echo"<div class='askButtonContainer'>
              <a href='./NewQuestionPage.php'><button class='askButton'>Ask Question</button></a>
            </div>";
      
        echo"<div class='card' data-id='65534'>
              <h3>$_SESSION[userName]</h3>
              <a href='./NewQuestionPage.php'><h2>What is your Question?</h2></a>
            </div>";
      }

      print"<div id='cardContainer'>";

      //連接資料庫
      //只要此頁面上有用到連接MySQL就要include它
      include("config.php");

      //搜尋資料庫資料
      $sql = "SELECT * FROM qTable ORDER BY qID DESC"; // Last entry First out :)
      $result = mysqli_query($link, $sql);  // $link from config.php , save the result to $result
      
      //Display the Questions from qTable
      if(mysqli_num_rows($result) > 0)
      {
        while($row = mysqli_fetch_array($result)) 
        {
          $qID = $row['qID'];
          $qUp = json_decode($row['qUp']);
          $qAnswer = json_decode($row['qAnswer']);
          echo "<div class='card' data-id='".count($qUp)."' id='".$qID."'>
                  <h4>".$row['qSpace']."</h4>
                  <div class='leftSpan'>
                    <span style='display:none'>qCreatorID: ".$row['qCreatorID']."</span>
                    <h3>".$row['qCreatorName']."</h3>
                    <h5>".$row['qTime']."</h5>
                  </div>
            
                  <div class='rightSpan'>       
                    <form action='QuestionDetailPage.php' method='POST'>  
                      <input type='hidden' name='redirectQID' value=".$row['qID'].">
                      <input type='submit' name='submit' value=".$row['qTitle'].">
                    </form>
                    <p>".$row['qContent']."</p>
                  </div>
            
                  <div>
                    <button id='upBtn+".$row['qID']."' name='".$row['qID']."' onclick='upvote(this)' >Upvote (".count($qUp).")</button>
                    <button id='answerBtn+".$row['qID']."' name='".$row['qID']."' onclick='showAnswer(this)'>Answer (".count($qAnswer).")</button>
                  </div>
              " ;
          $sql2 = "SELECT * FROM ansTable WHERE ansQID = '$qID' ORDER BY ansID DESC"; // Last entry First out :)
          $result2 = mysqli_query($link, $sql2);  // $link from config.php , save the result to $result
          //Display the Questions from qTable
          if(mysqli_num_rows($result2) > 0)
          {
            echo"<div class='ansContainer noShow' id='ans_".$qID."'>";

            while($row2 = mysqli_fetch_array($result2)) 
            {
              echo"<div class='answerCard' >
                    <span>".$row2['ansUserName']."</span>
                    <span>posted on ".$row2['ansTime']."</span>
                    <br/><br/>
                    <div class='ansDiv'>
                      ".$row2['ansContent']."
                    </div>
                  </div>";
            }
            if($_SESSION['user_logged_in'] === true) {
              echo"<div class='answerCard'>
                    <h3>$_SESSION[userName]</h3>
                    <button onclick='showInput(this)' name='".$qID."'><h2>Post your new answer.</h2></button>
                    <div id='ansDiv_".$qID."' class='ansDiv noShow' >
                      <form action='./answerFunction.php' method='POST'>
                        <input type='hidden' name='ansQID' value=".$row['qID'].">
                        <textarea name='ansContent' id='ansContent_".$qID."' style='width:35em; height:8em;' required></textarea>
                        <br/><br/>
                        <input type='submit' name='ansButton' value='Submit' />
                      </form>
                      <button onclick='hideInput(this)' name='".$qID."'>Cancel</button>
                    </div>
                  </div>";
            }
            print "</div>";
          } 
          
          else 
          {
            echo"<div class='ansContainer noShow' id='ans_".$qID."'>";

            if($_SESSION['user_logged_in'] === true) {
              echo"<div class='answerCard'>
                    <h3>$_SESSION[userName]</h3>
                    <button onclick='showInput(this)' name='".$qID."'><h2>Post your new answer.</h2></button>
                    <div id='ansDiv_".$qID."' class='ansDiv noShow' >
                      <form action='./answerFunction.php' method='POST'>
                        <input type='hidden' name='ansQID' value=".$row['qID'].">
                        <textarea name='ansContent' id='ansContent_".$qID."' style='width:35em; height:8em;' required></textarea>
                        <br/><br/>
                        <input type='submit' name='ansButton' value='Submit' />
                      </form>
                      <button onclick='hideInput(this)' name='".$qID."'>Cancel</button>
                    </div>
                  </div>";
            }
            print "</div>";
          }

          print "</div>";
        }
      }
      print"</div>";
    ?>

  </main>
  
  <script>

    function showAnswer(para){
      let qID = para.parentNode.parentNode.id;
      let answerContainer = document.getElementById('ans_'+qID);
      answerContainer.classList.remove('noShow');
    }

    function showInput(para){
      let ansDivID = document.getElementById('ansDiv_'+para.name)
      ansDivID.classList.remove('noShow');
    }

    function hideInput(para){
      let qID = para.name;
      para.parentNode.classList.add('noShow');
      document.getElementById('ansContent_'+qID).value ='';

    }

    function hot(){
      var aDiv = document.getElementsByClassName('card');
      var arr = [];
      for(var i=0;i<aDiv.length;i++)
      {
          arr.push(aDiv[i]);  //aDiv是元素的集合，并不是数组，所以不能直接用数组的baisort进行排序。
      }
      arr.sort(function(a,b){return b.getAttribute('data-id') - a.getAttribute('data-id')});
      for(var i=0;i<arr.length;i++)
      {
          let hi = document.getElementById('cardContainer')
          hi.appendChild(arr[i]); //将排好序的元素，重新塞到body里面显示。
      }
    }

    // function hotFilter(){
    //   let cardContainer = document.getElementById('cardContainer');
    //   cardContainer.innerHTML = "";
        
    //   var xmlhttp = new XMLHttpRequest();
          
    //   xmlhttp.open("POST", "MainPageFunction.php", true);
    //   xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //   xmlhttp.send("filter=Hot");

    //   xmlhttp.onreadystatechange = function () {
    //     if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
    //       var mesgs = document.getElementById("cardContainer");
    //       mesgs.innerHTML = xmlhttp.responseText;
    //     }
    //   }
    // }

    function upvote(para){
      
      var xmlhttp = new XMLHttpRequest();
          
      xmlhttp.open("POST", "upvote.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xmlhttp.send("upvote="+para.name);

      xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          var upBtn = document.getElementById(para.id);
          upBtn.innerHTML = xmlhttp.responseText;
        }
      }
    }

    function navSearch(str){
      let cardContainer = document.getElementById('cardContainer');
      cardContainer.innerHTML = "";
        
      var xmlhttp = new XMLHttpRequest();
          
      xmlhttp.open("POST", "MainPageFunction.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xmlhttp.send("navSearch="+str);

      xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          var mesgs = document.getElementById("cardContainer");
          mesgs.innerHTML = xmlhttp.responseText;
        }
      }
    }

    function algorithmFilter(){
      let cardContainer = document.getElementById('cardContainer');
      cardContainer.innerHTML = "";
        
      var xmlhttp = new XMLHttpRequest();
          
      xmlhttp.open("POST", "MainPageFunction.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xmlhttp.send("filter=Algorithm");

      xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          var mesgs = document.getElementById("cardContainer");
          mesgs.innerHTML = xmlhttp.responseText;
        }
      }
    }

    function MLFilter(){
      let cardContainer = document.getElementById('cardContainer');
      cardContainer.innerHTML = "";
        
      var xmlhttp = new XMLHttpRequest();
          
      xmlhttp.open("POST", "MainPageFunction.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xmlhttp.send("filter=ML");

      xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          var mesgs = document.getElementById("cardContainer");
          mesgs.innerHTML = xmlhttp.responseText;
        }
      }
    }

    function SystemFilter(){
      let cardContainer = document.getElementById('cardContainer');
      cardContainer.innerHTML = "";
        
      var xmlhttp = new XMLHttpRequest();
          
      xmlhttp.open("POST", "MainPageFunction.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xmlhttp.send("filter=System");

      xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          var mesgs = document.getElementById("cardContainer");
          mesgs.innerHTML = xmlhttp.responseText;
        }
      }
    }

    function JavascriptFilter(){
      let cardContainer = document.getElementById('cardContainer');
      cardContainer.innerHTML = "";
        
      var xmlhttp = new XMLHttpRequest();
          
      xmlhttp.open("POST", "MainPageFunction.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xmlhttp.send("filter=Javascript");

      xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          var mesgs = document.getElementById("cardContainer");
          mesgs.innerHTML = xmlhttp.responseText;
        }
      }
    }
    
  </script>
</body>
</html>
