import JSEncrypt from 'JSEncrypt';
import CryptoJs from 'crypto-js';
async function generateKey(){
    const keyPair = await window.crypto.subtle.generateKey({
        name: "RSA-OAEP",
        modulusLength: 4096,
        // binary representation of 65537
        publicExponent: new Uint8Array([1, 0, 1]),
        hash: "SHA-256",
    }, true, ['encrypt', 'decrypt']);
    const publicKey = keyPair.publicKey;
    const exportedPub = await window.crypto.subtle.exportKey("spki", publicKey);
    const encodedPub = String.fromCharCode.apply(null, Array.from(new Uint8Array(exportedPub)));
    const asciiPublicKey = window.btoa(encodedPub);
    const RpubKey = [
        "-----BEGIN PUBLIC KEY-----",
        asciiPublicKey.replace(/(.{80})/g, "$1\n"),
        "-----END PUBLIC KEY-----"
    ].join("\n");
    const privateKey = keyPair.privateKey;
    const exportedPriv = await window.crypto.subtle.exportKey("pkcs8", privateKey);
    const encodedPriv = String.fromCharCode.apply(null, Array.from(new Uint8Array(exportedPriv)));
    const asciiPrivKey = window.btoa(encodedPriv);
    const RprivKey = [
    "-----BEGIN PRIVATE KEY-----",
    asciiPrivKey.replace(/(.{80})/g, "$1\n"),
    "-----END PRIVATE KEY-----"
    ].join("\n");
    return { "public_key": RpubKey, "private_key":RprivKey};
    // const forge = require('node-forge');
    // // Generate key pair using crypto.subtle
    // crypto.subtle.generateKey(
    // {
    //     name: "RSA-OAEP",
    //     modulusLength: 2048,
    //     publicExponent: new Uint8Array([0x01, 0x00, 0x01]),
    //     hash: "SHA-256"
    // },
    // true,
    // ["encrypt", "decrypt"]
    // )
    // .then(keyPair => {
    // const privateKey = keyPair.privateKey;
    // const publicKey = keyPair.publicKey;

    // // Convert key pair to PEM format using forge
    // const privateKeyPem = forge.pki.setRsaPrivateKey(privateKey);
    // const publicKeyPem = forge.pki.setRsaPublicKey(publicKey);

    // // Use the PEM-formatted key pair with JSEncrypt
    // const jsEncrypt = new JSEncrypt();
    // jsEncrypt.setPrivateKey(privateKeyPem);
    // jsEncrypt.setPublicKey(publicKeyPem);

    // // Now you can use JSEncrypt to encrypt and decrypt data
    // const encryptedData = jsEncrypt.encrypt("Hello, World!");
    // console.log(encryptedData);
    // });
}
export async function encrypt(data: any){
    // function createTempFile(content, name){
        
    //     return File;
    // }
    // const keyPair = await generateKey();
    // //make checksum
    // const checksum = 
    // //make signature

    // //encrypt fileContent
    // const data[''];
    // createTempFile();
    // const ranEn = CryptoJs.AES.encrypt('message', 'secret key');
    // var encrypt = new JSEncrypt(); // Create a new instance of the JSEncrypt library.
    // encrypt.setPublicKey(keyPair['public_key']); // Set the public key for the encryption library.
    // var encrypted = encrypt.encrypt(data['fileContent']); // Use the encrypt method of the library to encrypt the data.
    // encrypt.s
    // return { 'file': file, 'checksum': checksum}; // Return the encrypted data.
}