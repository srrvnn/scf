<?php

  $msg_callback = '';
  $boolean_callback = False;

  $company_information = array();

  if(array_key_exists('input_company_name', $_POST)) {

    $msg_callback = 'Will fetch stock and news for '.$_POST['input_company_name'];    

    $name_company = $_POST['input_company_name'];

    $url_stocks = 'http://query.yahooapis.com/v1/public/yql?q=Select%20Name%2C%20Symbol%2C%20LastTradePriceOnly%2C%20Change%2C%20ChangeinPercent%2C%20PreviousClose%2C%20DaysLow%2C%20DaysHigh%2C%20Open%2C%20YearLow%2C%20YearHigh%2C%20Bid%2C%20Ask%2C%20AverageDailyVolume%2C%20OneyrTargetPrice%2C%20MarketCapitalization%2C%20Volume%2C%20Open%2C%20YearLow%20from%20yahoo.finance.quotes%20where%20symbol%3D%22'.$name_company.'%22&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';

    $url_news = 'http://feeds.finance.yahoo.com/rss/2.0/headline?s='.$name_company.'&region=US&lang=en-US';

    $company_information['url_stocks'] = $url_stocks;
    $company_information['url_news'] = $url_news;    

    $xml_stocks = simplexml_load_file($url_stocks);
    $xml_news = simplexml_load_file($url_news);

    // check for a non existant company   

    $boolean_emptyXML = False;

    if(strlen($xml_stocks->results->quote->Bid) < 1) {

        $boolean_emptyXML = True;
    }

    if($boolean_emptyXML) {

      $msg_callback = 'Stock Information Not Available';

    } else {

      $msg_callback = 'Search Results';
      $boolean_callback = 'True';

      $company_information["NameSymbol"] = $xml_stocks->results->quote->Name."(".$xml_stocks->results->quote->Symbol.")";      
      $company_information["Price"] = number_format(floatval($xml_stocks->results->quote->LastTradePriceOnly), 2, '.', ',');

      $plusminus = array("+","-");

      if(strpos($xml_stocks->results->quote->Change,'+') !== false) {

        $company_information["Change"] = '<img src="up_g.gif" /> <span class="txt_change color_green">'
          .str_replace($plusminus, "", $xml_stocks->results->quote->Change."(".$xml_stocks->results->quote->ChangeinPercent.")").'</span>';
      } else {

        $company_information["Change"] = '<img src="down_r.gif" /> <span class="txt_change color_red">'
          .str_replace($plusminus, "", $xml_stocks->results->quote->Change."(".$xml_stocks->results->quote->ChangeinPercent.")").'</span>';
      }     

      $company_information["Close"] = number_format(floatval($xml_stocks->results->quote->PreviousClose), 2, '.', ',');
      $company_information["DaysRange"] = number_format(floatval($xml_stocks->results->quote->DaysLow), 2, '.', ',')
          ." - ".number_format(floatval($xml_stocks->results->quote->DaysHigh), 2, '.', ',');;
      
      $company_information["Open"] = number_format(floatval($xml_stocks->results->quote->Open), 2, '.', ',');;
      $company_information["52wkRange"] = number_format(floatval($xml_stocks->results->quote->YearLow), 2, '.', ',')
          ." - ".number_format(floatval($xml_stocks->results->quote->YearHigh), 2, '.', ',');;

      $company_information["Bid"] = number_format(floatval($xml_stocks->results->quote->Bid), 2, '.', ',');;
      $company_information["Volume"] = number_format(floatval($xml_stocks->results->quote->Volume), 0, '.', ',');  ;

      $company_information["Ask"] = number_format(floatval($xml_stocks->results->quote->Ask), 2, '.', ',');;
      $company_information["AvgVol(3m)"] = number_format(floatval($xml_stocks->results->quote->AverageDailyVolume), 0, '.', ',');;

      $company_information["1yTargetList"] = number_format(floatval($xml_stocks->results->quote->OneyrTargetPrice), 2, '.', ',');;
      $company_information["MarketCap"] = $xml_stocks->results->quote->MarketCapitalization;

      $company_information["News"] = "";

      foreach ($xml_news->channel->item as $newslink):
        $newslinkinAnchor = '<li> <a href="'.$newslink->link.'" target="_blank">'.$newslink->title.'</a> </li>';
        $company_information["News"] = $company_information["News"].$newslinkinAnchor;
      endforeach;  

      // $content_stocks = htmlspecialchars($url_stocks);
      // $content_news = htmlspecialchars($url_news);
    }        

  } 

  ?>

<!DOCTYPE html>

<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Saravanan Ganesh / Courses / CS571 / Assignment 6</title>

    <style type="text/css">

      #form_xml_display {

        margin: 30px auto;
        text-align: center;
      }

      #input_company_name {

        margin: 20px 0px;
        width: 200px;      
      }

      #msg_callback, #content_stocks, #content_news {

        margin: 20px 0px;
        text-align: center;

      }

      #table_stocks, #table_news {

        margin: 20px auto;
        width: 55%;
      }

      #table_stocks tr td:nth-child(3) {

        padding-left: 25px;
      }

      .txt_heading {

        font-size: 1.2em;
        font-weight: bold;
      }

      .row_heading td {

        border-bottom: 2px solid black;
      }

      #table_stocks .value {

        text-align: right;
      }

      .color_green {

        color: green;
      }

      .color_red {

        color: red;
      }

      img {

        padding-left: 5px;
      }

    </style>

  </head>

  <body>

    <div id="container">

      <form id="form_xml_display" action="homework6.php" method="POST" onsubmit="return checkForm()">

        <p class="txt_heading"> Market Stock Search </p>
        <span> Company Symbol: </span>
        <input type="text" id="input_company_name" name="input_company_name"/> 
        <input type="submit" value="Search"/>
        <p> Example: GOOG, MSFT, YHOO, FB, AAPL, ... etc </p>       

        </form>

      <div id="msg_callback" class="txt_heading"> 

        <?php echo $msg_callback ?> 

        </div>    

      <div id="msg_callback" style="display:none">

          <?php if (isset($company_information['url_stocks'])) 
            echo htmlspecialchars($company_information['url_stocks'] )?> 

        </div>    

      <div id="msg_callback" style="display:none"> 

          <?php if (isset($company_information['url_news'])) 
            echo htmlspecialchars($company_information['url_news']) ?> 
        </div>          

      <div id="content" <?php if(!$boolean_callback) echo 'style="display:none"'?> >

        <table id="table_stocks"> 

          <tr class="row_heading"> 

            <td colspan="4"> 

              <span class="txt_heading"> <?php echo $company_information["NameSymbol"]?> </span> 
              <?php echo $company_information["Price"].$company_information["Change"] ?>
              </td>          

            </tr>

          <tr>

            <td> Prev Close: </td>
            <td class="value"> <?php echo $company_information["Close"] ?> </td>
            <td> Day's Range: </td>
            <td class="value"> <?php echo $company_information["DaysRange"] ?> </td>

          </tr>

          <tr>

            <td> Open: </td>
            <td class="value"> <?php echo $company_information["Open"] ?> </td>
            <td> 52wk Range: </td>
            <td class="value"> <?php echo $company_information["52wkRange"] ?> </td>

          </tr>

          <tr>

            <td> Bid: </td>
            <td class="value"> <?php echo $company_information["Bid"] ?> </td>
            <td> Volume: </td>
            <td class="value"> <?php echo $company_information["Volume"] ?> </td>

          </tr>

          <tr>

            <td> Ask: </td>
            <td class="value"> <?php echo $company_information["Ask"] ?> </td>
            <td> Avg Vol (3m): </td>
            <td class="value"> <?php echo $company_information["AvgVol(3m)"] ?> </td>

          </tr>

          <tr>

            <td> 1y Target Est: </td>
            <td class="value"> <?php echo $company_information["1yTargetList"] ?> </td>
            <td> Market Cap: </td>
            <td class="value"> <?php echo $company_information["MarketCap"] ?> </td>

          </tr>

        </table>       

        <table id="table_news">

          <tr class="row_heading"> 

            <td class="txt_heading"> News Headlines </td>

          </tr>   

          <tr> 

            <td> <ul> <?php echo $company_information["News"] ?> </ul> </td>

          </tr>
        </table>

        </div>    

    <script type=text/javascript> 

      function checkForm() {

        var company_name = document.getElementById('input_company_name').value;

        if (company_name.length < 1 ) {

          alert("Please enter a company name");
          return false;
        }       

      }
     
    </script>


    <noscript>

  </body>
</html>