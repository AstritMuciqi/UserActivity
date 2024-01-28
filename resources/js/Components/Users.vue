

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <ApplicationLogo class="block h-12 w-auto"/>
            <div v-if="$page.props.auth.user.role === 'super_admin'">
                <h2 class="mt-3 text-2xl">Filter by select:</h2>
                <div class="mt-4 mb-3 space-x-8 d-flex align-items-center">
                    <div class="form-check">
                        <input type="radio" id="usersRadio" name="userType" value="user" checked="checked" class="form-check-input" v-model="selectedRole">
                        <label for="usersRadio" class="form-check-label">Users</label>
                    </div>

                    <div class="form-check">
                        <input type="radio" id="adminsRadio" name="userType" value="admin" class="form-check-input" v-model="selectedRole">
                        <label for="adminsRadio" class="form-check-label">Admin's</label>
                    </div>
                </div>
            </div>


            <h1 class="mt-8 mb-3 text-2xl font-medium text-gray-900">
                <span v-if="users.length > 0 && users[0].role" style="text-transform: uppercase">{{users[0].role}}S LIST</span>
            </h1>


            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Balance</th>
                    <th v-if="$page.props.auth.user.role === 'super_admin'" scope="col" style="text-align: center;">Switch Role To</th>
                    <th v-if="$page.props.auth.user.role === 'super_admin'" scope="col" style="text-align: center;">Action</th>

                </tr>
                </thead>
                <tbody>
                <tr v-for="(user, index) in users" :key="user.id">
                    <th scope="row">{{ index + 1 }}</th>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td style="max-width: 4rem" v-if="!user.editing" @click="startEditing(user)">{{ user.bilance }} <i class="bi bi-pencil-square"></i></td>
                    <td style="max-width: 4rem; background-color: goldenrod;" v-else>
                        <input type="number" v-model="user.editedBalance" @blur="stopEditing(user)" @keyup.enter="stopEditing(user)">
                    </td>
                    <td v-if="$page.props.auth.user.role === 'super_admin'" style="text-align: center;">
                        <primary-button @click="switchRole(user, 'user')" v-if="user.role === 'admin'">Switch to User</primary-button>
                        <primary-button @click="switchRole(user, 'admin')" v-if="user.role === 'user'">Switch to Admin</primary-button>
                    </td>
                    <td v-if="$page.props.auth.user.role === 'super_admin'" style="text-align: center;">
                        <danger-button @click="deleteUser(user)" >Delete</danger-button>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
</template>


<script setup>
import { ref, onMounted, watch } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import DangerButton from "@/Components/DangerButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Import Toastr library
import toastr from 'toastr';

const users = ref([]);
const selectedRole = ref('user');


onMounted(async () => {
    // Fetch data from the Laravel API endpoint based on the selected role
    await fetchUsers();
});

watch(() => selectedRole.value, async (newValue) => {
    // Fetch data whenever selectedRole changes
    await fetchUsers();
    // Check if the user is in the 'admin' role


});

const startEditing = (user) => {
    // Set the user's editing flag to true
    user.editing = true;
    // Store the original balance in a separate property
    user.editedBalance = user.bilance;
};

const stopEditing = async (user) => {
    // Set the user's editing flag to false
    user.editing = false;

    // Check if the balance has changed
    if (user.editedBalance !== user.bilance) {
        try {
            // Update the user's balance using your API
            const response = await fetch(`http://127.0.0.1:8000/api/update-user-balance/${user.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ editedBalance: user.editedBalance }),
            });

            const data = await response.json();

            // Handle the response, you can show a success message or update the UI accordingly
            console.log(data.message);
            // Display a Toastr notification on success
            toastr.success('User balance updated successfully');

            // Refresh the user list after updating the balance
            await fetchUsers();
        } catch (error) {
            console.error('Failed to update user balance', error);
            toastr.error('Failed to update user balance');

        }
    }
};


const fetchUsers = async () => {
    const response = await fetch(`http://127.0.0.1:8000/api/getUsers?roleName=${selectedRole.value}`);
    const data = await response.json();

    users.value = data.users;
};

const switchRole = async (user, newRole) => {

    console.log(newRole);
    const response = await fetch(`http://127.0.0.1:8000/api/switch-role/${user.id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ newRole }),
    });

    const data = await response.json();

    console.log(data.message);

    await fetchUsers();
};

const deleteUser = async (user) => {
    const response = await fetch(`http://127.0.0.1:8000/api/delete-user/${user.id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
    });

    if (response.ok) {
        console.log('User deleted successfully');

        await fetchUsers();
    } else {
        console.error('Failed to delete user');
    }
};


</script>
