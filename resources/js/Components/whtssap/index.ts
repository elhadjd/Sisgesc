import { create , Whatsapp , Message , SocketState } from "venom-bot";

class Sender {
    private client : Whatsapp

    constructor (){
        this.initialize()
    }

    private initialize(){

        const qr = (base64Qrimg: string)=>{
            console.log('Ola');
            
        }

        const status = (statusSession: string)=>{

        }

        const start = (client: Whatsapp)=>{
            this.client = client
        }

        create('ws-sender-dev', qr ,status).then((client)=> start(client)).
        catch((error)=> console.log(error)
        )
    }

}

export default Sender
