<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
// require __DIR__ .'/../vendor/autoload.php';
require '../vendor/autoload.php';


// require 'Slim/Slim.php';
// \Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */

$app = new \Slim\Slim(array
    (
        'debug' => true,
        'mode' => 'development'
    )
);


//Add authentication and Cache middlewares
$app->add(new \Authentication());

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 **/

// GET route

$json_output = array();
define('EXPIRY_PERIOD', '+15 days');

$app->get('/', function() use ($app) {

    //Set the response header to json
    $app->response->header('Content-Type', 'application/json');

    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "Nothing so see here. Move along.";
    
    $json_output['data']["name"] = "API - Catholic Mobile Application";
    $json_output['data']["version"] = '1.0';
    $json_output['data']["company"] = 'iQube Labs';
    $json_output['data']["author"] = "Akintewe Rotimi";
    $json_output['data']["date"] = "Thursday 21, August 2014";
    echo json_encode($json_output);

});

$app->post('/login', function() use ($app){
    
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $phone = $app->request->post('phone');
        $password = $app->request->post('password');

        if(isset($phone) && isset($password)) {
            
            $pass = hash('sha512', $password);
            $user = User::select('id', 'phone', 'password', 'firstname', 'lastname', 'email', 'auid')
                        ->whereRaw('phone = ? and password = ?', array($phone, $pass))
                        ->first();

            if(isset($user)) {
                
                $now = date('Y-m-d h:i:s');
                $token = hash('md5', $now);
                $expirydate = strtotime(EXPIRY_PERIOD);
                $str_expirydate = date('Y-m-d h:i:s', $expirydate);
                
                $userToken = Usertoken::where('user_id', '=', $user->id)->first();
                
                if(!isset($userToken))
                    $userToken = new Usertoken;

                $userToken->user_id = $user->id;
                $userToken->token = $token;
                $userToken->expires = $str_expirydate;
                $userToken->lastusedate = $now;
                $userToken->save();

                $json_output['meta']["status"] = 0;
                $json_output['meta']["message"] = "Login successfully";
                
                $userArray = $user->toArray();
                $userArray['token'] = $token;
                $json_output['data']['user'] = $userArray;

            } else {

                $json_output['meta']["status"] = 2;
                $json_output['meta']["message"] = "Invalid Phone and/or Password!";
            }

        } else {

            $json_output['meta']["status"] = 3;
            $json_output['meta']["message"] = "Phone or Password cannot be empty!";
        } 

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }
});


$app->get('/daily_devotions(/:year(/:month(/:day)))', function($year, $month, $day) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $formatted_date = $year.'-'.$month.'-'.$day;
        // echo $formatted_date;
        $dev_date = strtotime($formatted_date);
        $devotion = DailyDevotion::select('id', 'title', 'tag', 'date', 'long_date_description', 'full_date')
                                  ->where('date', '=', $dev_date)
                                  ->first();

        if($devotion) {
            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            
            $json_output['data']['devotion'] = $devotion->toArray();
        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No record found!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/daily_readings/:dev_id', function($dev_id) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $readings = DailyReading::select('id', 'daily_devotion_id', 'title', 'first_reading', 'responsorial_psalm', 'second_reading', 'alleluia', 'gospel_reading', 'meditation_for_day', 'entrance_antiphon', 'opening_prayer', 'prayer_over_offering', 'communion_antiphon', 'prayer_after_communion')
                                ->where('daily_devotion_id', '=', $dev_id)
                                ->first();

        if($readings) {
            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            
            $json_output['data']['daily_reading'] = $readings->toArray();
        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No record found!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/daily_readings(/:year(/:month(/:day)))', function($year, $month, $day) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $formatted_date = $year.'-'.$month.'-'.$day;
        // echo $formatted_date;th
        $dev_date = strtotime($formatted_date);
        $devotion = DailyDevotion::select('id', 'title', 'tag', 'date', 'long_date_description', 'full_date')
                                  ->where('date', '=', $dev_date)
                                  ->first();

        if(isset($devotion)) {
            $readings = DailyReading::select('id', 'daily_devotion_id', 'title', 'first_reading', 'responsorial_psalm', 'second_reading', 'alleluia', 'gospel_reading', 'meditation_for_day', 'entrance_antiphon', 'opening_prayer', 'prayer_over_offering', 'communion_antiphon', 'prayer_after_communion')
                                    ->where('daily_devotion_id', '=', $devotion->id)
                                    ->first();

            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            
            $json_output['data']['daily_devotion'] = $devotion->toArray();
            $json_output['data']['daily_reading'] = $readings->toArray();

        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No record found!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/masses/:dev_id', function($dev_id) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $mass = DailyReading::select('id', 'daily_devotion_id', 'title', 'first_reading', 'responsorial_psalm', 'second_reading', 'alleluia', 'gospel_reading', 'meditation_for_day', 'entrance_antiphon', 'opening_prayer', 'prayer_over_offering', 'communion_antiphon', 'prayer_after_communion')
                                ->where('daily_devotion_id', '=', $dev_id)
                                ->first();

        if($readings) {
            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            
            $json_output['data']['masses'] = $mass->toArray();
        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No record found!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/masses(/:year(/:month(/:day)))', function($year, $month, $day) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $formatted_date = $year.'-'.$month.'-'.$day;
        // echo $formatted_date;th
        $dev_date = strtotime($formatted_date);
        $devotion = DailyDevotion::select('id', 'title', 'tag', 'date', 'long_date_description', 'full_date')
                                  ->where('date', '=', $dev_date)
                                  ->first();

        if(isset($devotion)) {
            $mass = DailyReading::select('id', 'daily_devotion_id', 'title', 'first_reading', 'responsorial_psalm', 'second_reading', 'alleluia', 'gospel_reading', 'meditation_for_day', 'entrance_antiphon', 'opening_prayer', 'prayer_over_offering', 'communion_antiphon', 'prayer_after_communion')
                                    ->where('daily_devotion_id', '=', $devotion->id)
                                    ->first();

            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            

            $json_output['data']['daily_devotion'] = $devotion->toArray();

            if($mass) {
                $json_output['data']['masses'] = $mass->toArray();
            } else {
                $json_output['data']['masses'] = array();
            }

        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No devotion record found for the day!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/morning_prayers/:dev_id', function($dev_id) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $morning_prayers = MorningPrayer::select('id', 'daily_devotion_id', 'todays_rosary', 'morning_prayer', 'hymn', 'psalm', 'scripture_reading', 'intercessions')
                                    ->where('daily_devotion_id', '=', $dev_id)
                                    ->first();

        if($morning_prayers) {
            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            
            $json_output['data']['morning_prayers'] = $morning_prayers->toArray();
        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No record found!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/morning_prayers(/:year(/:month(/:day)))', function($year, $month, $day) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $formatted_date = $year.'-'.$month.'-'.$day;
        // echo $formatted_date;th
        $dev_date = strtotime($formatted_date);
        $devotion = DailyDevotion::select('id', 'title', 'tag', 'date', 'long_date_description', 'full_date')
                                  ->where('date', '=', $dev_date)
                                  ->first();

        if(isset($devotion)) {
            $morning_prayers = MorningPrayer::select('id', 'daily_devotion_id', 'todays_rosary', 'morning_prayer', 'hymn', 'psalm', 'scripture_reading', 'intercessions')
                                    ->where('daily_devotion_id', '=', $devotion->id)
                                    ->first();

            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            

            $json_output['data']['daily_devotion'] = $devotion->toArray();

            if($morning_prayers) {
                $json_output['data']['morning_prayers'] = $morning_prayers->toArray();
            } else {
                $json_output['data']['morning_prayers'] = array();
            }

        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No devotion record found for the day!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/midday_prayers/:dev_id', function($dev_id) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $midday_prayers = MiddayPrayer::select('id', 'title', 'angelus', 'hymn', 'psalm', 'scripture_reading')
                                    ->where('daily_devotion_id', '=', $dev_id)
                                    ->first();

        if($midday_prayers) {
            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            
            $json_output['data']['midday_prayers'] = $midday_prayers->toArray();
        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No record found!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/midday_prayers(/:year(/:month(/:day)))', function($year, $month, $day) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $formatted_date = $year.'-'.$month.'-'.$day;
        // echo $formatted_date;th
        $dev_date = strtotime($formatted_date);
        $devotion = DailyDevotion::select('id', 'title', 'tag', 'date', 'long_date_description', 'full_date')
                                  ->where('date', '=', $dev_date)
                                  ->first();

        if(isset($devotion)) {
            $midday_prayers = MiddayPrayer::select('id', 'title', 'angelus', 'hymn', 'psalm', 'scripture_reading')
                                    ->where('daily_devotion_id', '=', $devotion->id)
                                    ->first();

            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            

            $json_output['data']['daily_devotion'] = $devotion->toArray();

            if($midday_prayers) {
                $json_output['data']['midday_prayers'] = $midday_prayers->toArray();
            } else {
                $json_output['data']['midday_prayers'] = array();
            }

        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No devotion record found for the day!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/evening_prayers/:dev_id', function($dev_id) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $evening_prayers = EveningPrayer::select('id', 'title', 'evening_prayer', 'hymn', 'psalm', 'scripture_reading', 'intercessions')
                                    ->where('daily_devotion_id', '=', $dev_id)
                                    ->first();

        if($evening_prayers) {
            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            
            $json_output['data']['evening_prayers'] = $evening_prayers->toArray();
        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No record found!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/evening_prayers(/:year(/:month(/:day)))', function($year, $month, $day) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $formatted_date = $year.'-'.$month.'-'.$day;
        // echo $formatted_date;th
        $dev_date = strtotime($formatted_date);
        $devotion = DailyDevotion::select('id', 'title', 'tag', 'date', 'long_date_description', 'full_date')
                                  ->where('date', '=', $dev_date)
                                  ->first();

        if(isset($devotion)) {
            $evening_prayers = EveningPrayer::select('id', 'title', 'evening_prayer', 'hymn', 'psalm', 'scripture_reading', 'intercessions')
                                    ->where('daily_devotion_id', '=', $devotion->id)
                                    ->first();

            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            

            $json_output['data']['daily_devotion'] = $devotion->toArray();

            if($evening_prayers) {
                $json_output['data']['evening_prayers'] = $evening_prayers->toArray();
            } else {
                $json_output['data']['evening_prayers'] = array();
            }

        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No devotion record found for the day!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/divine_mercy_prayers/:dev_id', function($dev_id) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $divine_mercy_prayers = DivineMercyPrayer::select('id', 'title', 'divine_mercy_prayer', 'divine_mercy_praises')
                                    ->where('daily_devotion_id', '=', $dev_id)
                                    ->first();

        if($divine_mercy_prayers) {
            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            
            $json_output['data']['divine_mercy_prayers'] = $divine_mercy_prayers->toArray();
        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No record found!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/divine_mercy_prayers(/:year(/:month(/:day)))', function($year, $month, $day) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {
        
        $formatted_date = $year.'-'.$month.'-'.$day;
        // echo $formatted_date;th
        $dev_date = strtotime($formatted_date);
        $devotion = DailyDevotion::select('id', 'title', 'tag', 'date', 'long_date_description', 'full_date')
                                  ->where('date', '=', $dev_date)
                                  ->first();

        if(isset($devotion)) {
            $divine_mercy_prayers = DivineMercyPrayer::select('id', 'title', 'divine_mercy_prayer', 'divine_mercy_praises')
                                    ->where('daily_devotion_id', '=', $devotion->id)
                                    ->first();

            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";
            

            $json_output['data']['daily_devotion'] = $devotion->toArray();

            if($divine_mercy_prayers) {
                $json_output['data']['divine_mercy_prayers'] = $divine_mercy_prayers->toArray();
            } else {
                $json_output['data']['divine_mercy_prayers'] = array();
            }

        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No devotion record found for the day!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/night_prayers(/:year(/:month))', function($year, $month) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {

        if(isset($year) && isset($month)) {
            $night_prayers = NightPrayer::select('id', 'month', 'year', 'title', 'hymn', 'psalm', 'scripture_reading', 'responsory', 'gospel_canticle', 'concluding_prayer')
                                    ->where('year', '=', $year)
                                    ->where('month', '=', $month)
                                    ->first();

            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";

            if($night_prayers) {
                $json_output['data']['night_prayers'] = $night_prayers->toArray();
            } else {
                $json_output['data']['night_prayers'] = array();
            }

        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No devotion record found for the day!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/monthly_messages(/:year(/:month))', function($year, $month) use ($app){
  
    $json_output['meta']["status"] = 0;
    $json_output['meta']["message"] = "";

    try {

        if(isset($year) && isset($month)) {
            $monthly_messages = MonthlyMessage::select('id', 'month', 'year', 'month_reflection', 'editorial', 'know_your_faith')
                                    ->where('year', '=', $year)
                                    ->where('month', '=', $month)
                                    ->first();

            $json_output['meta']["status"] = 0;
            $json_output['meta']["message"] = "Request successful!";

            if(isset($monthly_messages)) {
                $json_output['data']['monthly_messages'] = $monthly_messages->toArray();
            } else {
                $json_output['data']['monthly_messages'] = array();
            }

        } else {
            $json_output['meta']["status"] = 12;
            $json_output['meta']["message"] = "No devotion record found for the day!";
        }

        $app->response->header('Content-Type', 'application/json');
        echo json_encode($json_output);

    } catch (Exception $e) {
        print_r($e->getMessage());
    }

});

$app->get('/checkdates(/:year(/:month(/:day)))', function($year, $month, $day) use ($app){

    $formatted_date = $year.'-'.$month.'-'.$day; 
    $longdate = strtotime($formatted_date);
    echo $longdate . ', ';
    echo date('Y-m-d', $longdate);
});

// $app->post('/user/authenticate', function() use ($app){

// });

// $app->get('/default/brands', function() use ($app) {

// });

// $app->get('/default/brandelements', function() use ($app) {

// });

// $app->get('/default/merchandise', function() use ($app) {

// });


// $app->get('/books', function() {
    
//     $row = RedBean::getRow( 'SELECT * FROM users WHERE id = :id', array(':id'=>4));
//     print_r($row);
// });

// $app->get('/archive(/:year(/:month(/:day)))', function ($year = 2010, $month = 12, $day = 05) {
//     echo sprintf('%s-%s-%s', $year, $month, $day);
// });



// $app->get('/hello/:name', function ($name) {
//     echo "Hello, $name";
// });

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
