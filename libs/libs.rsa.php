<?php

class Libs_Rsa {
    private $private_key_pem = "
-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDaLLn6voGSFL5a
JF25YCbsorUlWSa0fo65tnVKhttkSD3FCNOcUiumR7mGI9egguZw6W3k0SI763bG
1QYE+DvxkAAvSHsqXRApdXuDBv9EQ3zuiuwJrhW66bTd8vgOsEZgkvS27bOQweHk
zKv2+Dwub7mnGUffiYMeUESRmCyA4ermt19tD0tcLOTqVZvXrwCChFzzucl5vTyO
mNADUVodNyG9tRLWirkf3nm0SvXyq4RDrfIPxu6sqqsbBFFjYsuQBtuzWJGCJKkQ
7A1n7sUszvPor1uKGwskzNDBJsP+JaUjqzbL6OwXMQXOTghufUwSm5UwBCA8Pg6P
MBhNyncnAgMBAAECggEARodi794aAOFXz8glzEF7E9Bdgt9ZFcw702labWc/ESIR
Zdq+nbAqkCxrmyOHF0p5uFKdxfSdfoVV9lzy/zRIHIUSqjZiKoW/Lcfez7K5XpX6
kxqxx0dRmwTmBUIAUq9FcP7QwToCbG9g2RgZF1OUEObpO01+4JKRooEMSYzKBjdp
Xi8rciWhdFlsmWYXNWZn6aYyorg/Nhf3u4T02hL8DABU/dvKJkICgQxoz4qBk54n
Zhag5QftFnWZHAXN4HOR8sAHwAhNnp4mi4j4ETU+2L7hEGfzgV041fkySrad1iC1
c6e3wA/ZxbXhrB/IUKpwDunEU+xMuJOlb83Xrn3NcQKBgQDzIAW2bnvDMo/R3wEq
aujXawQZFiIRgbzdcjQGiZ85rxR1YeM+7AOEEkGCCHNo5dqdrzh+rPzNHKVDAAfu
FoBOPPKHEijm+i7fvBebgWozDSv6g/duHOwmaZqPAljz2NBSl8UOAezLqSnGHlh1
8bYFzpCKhpNGy6k+ZwO8TzhyMwKBgQDlunWDX/S2OZPIaCu26TCfwCkXdbyfUHyE
3aXpjns3z9ZwWZY7bCIhzbRvazpbHqeJ8fwACpg5mp9CJM+PO5BRN0/Y/bJK3gue
iRvWuP7nKsG3f7mMNhkXr05JhzWH4LeEhOzDmtcOgNWX308OlDxB+NBNE4aj4BU8
YgmdEmS7PQKBgBNkC3CYcUCJSNU5TxN+AWIlA+eoT0FMMq8Ky8F1uoBUSXJvQzui
gUlIV3cyi1njc0M+VWgtDeCiF0A3wWoo20TiMYm6Vg7OB20KyGs7sCewNkC9w79B
iUgrBgu/6S5RrZPYzPuoBaXcsRs/Elkd1SGzbu3Ne/OajL4QY0jinKLnAoGADwRN
QjU3jeHdozAMC3zazNaG2R29IrzkJttGSSojK9bOMbHpfCDpakREEzRzMBmk4gOL
cYU/xiQxXOWDG93M5UI0Wf7bdMFnCQn1Y+fb4ciNuS0EOAIhSccP2waXnTY3SNZX
k8G/ZRdO3wqd5FoURku5chBgsL2pJbVS442cQBECgYBoJTA/bZ+Phn1Fm8e7akvi
m5W0gOcZTZIjn2zrldqgiqXUTpeF4fHX9jjVG7mctjbjZpUyPKRKQmRyxEj3Z1/U
36oBla2q4L2+dGRuPiOLcLRO53fUzAtwGPimZt9liPeO6P52gEgd2gtyyAf7eS5e
7AIrPtwk9Tdad6lOYYHjSA==
-----END PRIVATE KEY-----
";
    private $public_key_pem = "
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA2iy5+r6BkhS+WiRduWAm
7KK1JVkmtH6OubZ1SobbZEg9xQjTnFIrpke5hiPXoILmcOlt5NEiO+t2xtUGBPg7
8ZAAL0h7Kl0QKXV7gwb/REN87orsCa4Vuum03fL4DrBGYJL0tu2zkMHh5Myr9vg8
Lm+5pxlH34mDHlBEkZgsgOHq5rdfbQ9LXCzk6lWb168AgoRc87nJeb08jpjQA1Fa
HTchvbUS1oq5H955tEr18quEQ63yD8burKqrGwRRY2LLkAbbs1iRgiSpEOwNZ+7F
LM7z6K9bihsLJMzQwSbD/iWlI6s2y+jsFzEFzk4Ibn1MEpuVMAQgPD4OjzAYTcp3
JwIDAQAB
-----END PUBLIC KEY-----
";

    public function encrypt($data)
    {
        if (!$data) {
            return false;
        }
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $private_key = $this->private_key_pem;
        openssl_private_encrypt($data, $crypted, $private_key);
        return base64_encode($crypted);
    }

    public function decrypt($data)
    {
        $public_key = $this->public_key_pem;
        $data = base64_decode($data);
        openssl_public_decrypt($data, $decrypted, $public_key);
        return $decrypted;
    }

}