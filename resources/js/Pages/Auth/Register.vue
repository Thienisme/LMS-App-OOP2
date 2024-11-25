<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 0,
    profile: null,  // Đặt là null ban đầu
    class: '',      // Thêm trường lớp
    student_code: '', // Thêm trường mã sinh viên
    phone: '',      // Thêm trường số điện thoại
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    console.log(file)
    if (file) {
        form.profile = file; // Gán file đã chọn vào form
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Full Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Thêm trường lớp -->
            <div class="mt-4">
                <InputLabel for="class" value="Class" />
                <TextInput
                    id="class"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.class"
                    required
                    autocomplete="class"
                />
                <InputError class="mt-2" :message="form.errors.class" />
            </div>

            <!-- Thêm trường mã sinh viên -->
            <div class="mt-4">
                <InputLabel for="student_code" value="Student Code" />
                <TextInput
                    id="student_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.student_code"
                    required
                    autocomplete="student_code"
                />
                <InputError class="mt-2" :message="form.errors.student_code" />
            </div>

            <!-- Thêm trường số điện thoại -->
            <div class="mt-4">
                <InputLabel for="phone" value="Phone Number" />
                <TextInput
                    id="phone"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    required
                    autocomplete="tel"
                    pattern="[0-9]{10}"
                    title="Please enter a valid 10-digit phone number"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4">
                <InputLabel for="profile" value="Profile Image" />
                <input
                    id="profile"
                    type="file"
                    class="mt-1 block w-full"
                    @change="handleFileChange"
                />
                <InputError class="mt-2" :message="form.errors.profile" />
            </div>

            <div class="flex flex-col gap-5 mt-4">
                <div>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Register
                    </PrimaryButton>
                </div>
            </div>

            <div>
                <hr class="mt-5">
                <p class="mt-5 mb-2 text-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                    Already have an account? <Link class="text-blue-500 hover:text-blue-600" :href="route('login')">Login</Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>