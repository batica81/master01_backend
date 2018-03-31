#!/bin/sh

printf '\n'c
printf 'Generisanje klijentskog sertifikata:\n'
printf '\n'
openssl req -new -passout pass:$2 -config client.conf -out certs/$1.csr -keyout certs/$1.key 

printf '\n'
printf 'Potpisivanje klijentskog sertifikata:\n'
printf '\n'
openssl ca -config ca.conf -batch -in certs/$1.csr -out certs/$1.crt -extensions client_ext

printf '\n'
printf 'Izvoz klijentskog sertifikata u p12 format:\n'
printf '\n'
openssl pkcs12 -export -clcerts -in certs/$1.crt -inkey certs/$1.key -passin pass:$2 -out certs/$1.p12 -passout pass:$2

printf '\n'
printf 'Izvoz klijentskog sertifikata u pem format:\n'
printf '\n'
openssl pkcs12 -in certs/$1.p12 -passin pass:$2 -out certs/$1.p12 -out certs/$1.pem -passout pass:$2 -clcerts

printf '\n'
printf 'Prikaz hash vrednosti klijentskog sertifikata:\n'
printf '\n'
openssl x509 -noout -fingerprint -sha1 -inform pem -in certs/$1.pem
