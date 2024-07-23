<?php
    class JWt{



        function base64UrlEncode($data) {
            return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
        }

        function base64UrlDecode($data) {
            return base64_decode(strtr($data, '-_', '+/'));
        }

        function getSecretKey(){
            return 'kaunghtettin17204&&wyne75707@@&&kk&&yy';
        }

        function createJWT($header, $payload) {
            // Encode Header
            $base64UrlHeader = $this->base64UrlEncode(json_encode($header));

            // Encode Payload
            $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));

            // Create Signature Hash
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->getSecretKey(), true);

            // Encode Signature to Base64Url
            $base64UrlSignature = $this->base64UrlEncode($signature);

            // Create JWT
            $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

            return $jwt;
        }

        // Example Usage
        // $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        // $payload = ['userId' => 123, 'username' => 'yourusername', 'exp' => time() + 3600];
        // $this->getSecretKey() = 'your_secret_key';

        // $jwt = createJWT($header, $payload, $this->getSecretKey());
        // echo $jwt;

        function validateJWT($jwt) {
            // Split the JWT into its three parts
            $tokenParts = explode('.', $jwt);
            $header = $this->base64UrlDecode($tokenParts[0]);
            $payload = $this->base64UrlDecode($tokenParts[1]);
            $signatureProvided = $tokenParts[2];

            // Decode the JSON objects
            $headerDecoded = json_decode($header, true);
            $payloadDecoded = json_decode($payload, true);

            // Check the expiration time
            if (isset($payloadDecoded['exp']) && $payloadDecoded['exp'] < time()+(60*60*24*30)) {
                return false;
            }

            // Build a signature based on the header and payload using the secret
            $base64UrlHeader = $this->base64UrlEncode($header);
            $base64UrlPayload = $this->base64UrlEncode($payload);
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->getSecretKey(), true);
            $base64UrlSignature = $this->base64UrlEncode($signature);

            // Verify it matches the signature provided in the JWT
            if($base64UrlSignature === $signatureProvided){
                return $payloadDecoded;
            }else{
                return false;
            }
        }
        // Example Usage
        //$isValid = validateJWT($jwt, $this->getSecretKey());
        //echo $isValid ? 'JWT is valid' : 'JWT is invalid';
    }
?>