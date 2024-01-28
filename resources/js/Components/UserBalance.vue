<template>
    <div>
        <!-- Card component with data and balance -->
        <div class="card">
            <div class="card-header">
                My Data
            </div>
            <div class="card-body">
                <!-- Display your data here -->
                <div>
                    <p><strong>Name:</strong> {{ $page.props.auth.user.name }}</p>
                    <p><strong>Email:</strong> <span id="userEmail">{{ $page.props.auth.user.email }}</span></p>
                    <!-- Add more data fields as needed -->

                    <p  v-if="data"><strong>Bilanci:</strong> <span id="valueBilanci">{{ data }}</span> </p>

                    <secondary-button style="margin-top:10px;" @click="openModal">Send Balance To Specific User</secondary-button>

                </div>

                <!-- Modal Section -->
                <div v-if="showModal" class="modal">
                    <div class="modal-content" @click.stop>
                        <h2>Select User</h2>
                        <label for="recipient">Recipient:</label>

                        <select v-model="selectedUser">
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                        <div style="display: flex; justify-content: space-between;">
                            <button style="width: 48%;" class="btn btn-success" @click="sendBalance(selectedUser)">Send Balance</button>
                            <button style="width: 48%;" class="btn btn-secondary" @click="closeModal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import SecondaryButton from "@/Components/SecondaryButton.vue";
import toastr from 'toastr';

const data = ref(null);
const users = ref(null);
const showModal = ref(false);

onMounted(async () => {
    const emailElement = document.getElementById('userEmail');

    if (emailElement) {
        const userEmail = emailElement.textContent.trim();
        await fetchData(userEmail);
    } else {
        console.error('Email element not found or invalid.');
    }
    await fetchUsers();
});

const openModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const fetchUsers = async () => {
    const response = await fetch(`http://127.0.0.1:8000/api/getUsers?roleName=user`);
    const result = await response.json();

    const emailElement = document.getElementById('userEmail');
    const userEmail = emailElement.textContent.trim();

    const filteredUsers = result.users.filter(user => user.email !== userEmail);

    users.value = filteredUsers;
};

const fetchData = async (userEmail) => {
    const response = await fetch(`http://127.0.0.1:8000/api/users-with-balance/${userEmail}`);
    const result = await response.json();
    console.log(result);
    data.value = result.users.bilance;
};



const sendBalance = async (selectedUser) => {

    const valueBilanci1 = document.getElementById('valueBilanci');


    const valueBilanci = valueBilanci1.textContent.trim();

    const emailElement1 = document.getElementById('userEmail');

    const emailElement = emailElement1.textContent.trim();


    const response = await fetch(`http://127.0.0.1:8000/api/send-balance/${selectedUser}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ valueBilanci, selectedUser,emailElement}),
    });

    const data = await response.json();

    console.log(data.message);

    closeModal();

    await fetchUsers();

    toastr.success('User balance moved successfully');
};

</script>

<style scoped>
.card {
    border: 1px solid #ddd;
    border-radius: 8px;
    margin: 20px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.card-header {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.card-body {
    font-size: 1.2rem;
}

.modal {
    position: fixed;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    width: 50%;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

select {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
}

.button-container {
    display: flex;
    justify-content: space-between;
}

</style>
