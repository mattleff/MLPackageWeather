<?php

class SF_controller_weather extends SF_system_controller_twig_template implements SF_interface_controller_router
{
	public $view = "weather/forecast.html";
	
	function doForecast( $slug_array )
	{
		$input = self::getInput( array(
			"zip" => FILTER_VALIDATE_INT,
		) );
		if( $input['zip'] ) {
			$this->set( 'zip', $input['zip'] );
			
			$weatherData = $this->getWeatherData( $this->get( 'zip' ), new DateTime()  );
			$this->set( 'weather_data', $weatherData->toArray() );
		}
		
		$this->set( 'available_forecast_type', array( "today" ) );
	}
	
	private function getWeatherData( $zip_code, DateTime $date )
	{
		$weatherData = DB_weather_forecastsQuery::create()->filterByZipCode( $zip_code )->filterByDate( $date )->findOneOrCreate();
		if( $weatherData->isNew() ) {
			//...request from weather data service the forecast: fake it!
			$dataFromAPI = array( "HighTemperature" => 78, "LowTemperature" => 62, "ChancePrecipitation" => 12 );
			$weatherData->fromArray( $dataFromAPI );
			$weatherData->save();
		}
		return $weatherData;
	}
}