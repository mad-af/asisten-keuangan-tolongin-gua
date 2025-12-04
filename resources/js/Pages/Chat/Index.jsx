import { useForm } from '@inertiajs/inertia-react';
import React, { useState } from 'react'
import Sidebar from '../../Components/WhatsApp/SideBar';
import ChatWindow from '../../Components/WhatsApp/ChatWindow';

const Index = () => {

    return (
        <div className="h-screen w-full bg-base-200 flex">
            <Sidebar />
            <ChatWindow />
        </div>
    )
}

export default Index