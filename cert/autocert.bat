
set arg1=%1
set arg2=%2

shift
shift

openssl req -new -passout pass:%arg2% -config client.conf -out certs/%arg1%.csr -keyout certs/%arg1%.key 

openssl ca -config ca.conf -batch -in certs/%arg1%.csr -out certs/%arg1%.crt -extensions client_ext

openssl pkcs12 -export -clcerts -in certs/%arg1%.crt -inkey certs/%arg1%.key -passin pass:%arg2% -out certs/%arg1%.p12 -passout pass:%arg2%

openssl pkcs12 -in certs/%arg1%.p12 -passin pass:%arg2% -out certs/%arg1%.p12 -out certs/%arg1%.pem -passout pass:%arg2% -clcerts

openssl x509 -noout -fingerprint -sha1 -inform pem -in certs/%arg1%.pem
