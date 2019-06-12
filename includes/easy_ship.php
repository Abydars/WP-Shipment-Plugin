<?php
include './shipment.php';

class EasyShip extends Shipment
{
    public function create_shipment($data)
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.easyship.com/shipment/v1/shipments");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, "{
              \"platform_name\": \"null\",
          \"platform_order_number\": \"#1234\",
          \"selected_courier_id\": \"$data->courier_id\",
          \"destination_country_alpha2\": \"$data->country\",
          \"destination_city\": \"$data->city\",
          \"destination_postal_code\": \"$data->postal_code\",
          \"destination_state\": \"$data->state\",
          \"destination_name\": \"$data->name\",
          \"destination_address_line_1\": \"$data->address\",
          \"destination_address_line_2\": null,
          \"destination_phone_number\": \"$data->number\",
          \"destination_email_address\": \"$data->email\",
          \"items\": [
            {
              \"description\": \"$data->description\",
              \"sku\": \"$data->sku\",
              \"actual_weight\": $data->weight,
              \"height\": $data->height,
              \"width\": $data->width,
              \"length\": $data->length,
              \"declared_currency\": \"$data->currency\",
              \"declared_customs_value\": $data->cusotm_value
            }
          ]
        }");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer sand_AUbUTCXZBA4s+2mA1E77xZTNfLHiQGBV22ROrZ8S6jo="
        ));

        $api_response = curl_exec($ch);
        curl_close($ch);

        var_dump($api_response);

//        $api_response = "";



        if ($api_response->error) {
            $response['status'] = false;
            $response['message'] = $api_response->error;
        } else {
            $response['status'] = true;
            $response['data'] = $api_response->shipment;
        }

        return $response;
    }

    public function delete_shipment($id)
    {
        // TODO: Implement deleteShipment() method.
    }

    public function get_shipment($id)
    {

    }
    function get_rate($data){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.easyship.com/rate/v1/rates");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, "{
  \"origin_country_alpha2\": \"SG\",
  \"origin_postal_code\": \"WC2N\",
  \"destination_country_alpha2\": \"US\",
  \"destination_postal_code\": \"10030\",
  \"taxes_duties_paid_by\": \"Sender\",
  \"is_insured\": false,
  \"items\": [
    {
      \"actual_weight\": 1.2,
      \"height\": 10,
      \"width\": 15,
      \"length\": 20,
      \"category\": \"mobiles\",
      \"declared_currency\": \"SGD\",
      \"declared_customs_value\": 100
    }
  ]
}");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer 4e2b327e2ef5471885cd0bc50a0c9fe52481793bd309b2c4f2a6bdac3f10ae1f"
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        var_dump($response);
    }
}