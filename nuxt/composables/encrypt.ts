import { createCipheriv, createDecipheriv } from 'crypto';
const algorithm = 'aes-256-cbc';
const secretKey = 'your_secret_key_here';

export async function encrypt(){
    var encrypt = new JSEncrypt(); // Create a new instance of the JSEncrypt library.
    var publicKey = “—–BEGIN PUBLIC KEY—– […] —–END PUBLIC KEY—–“; // Define the public key. This is the public key generated earlier.
    encrypt.setPublicKey(publicKey); // Set the public key for the encryption library.
    var encrypted = encrypt.encrypt(data); // Use the encrypt method of the library to encrypt the data.
    encrypt.s
    return encrypted; // Return the encrypted data.
}