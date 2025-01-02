<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    profile_img: null,
    class: user.class || '',
    student_code: user.student_code || '',
    phone: user.phone || '',
});

// Ref để lưu trữ file input
const fileInput = ref(null);
// Ref để lưu URL preview của ảnh
const previewImage = ref(null);

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Kiểm tra kích thước file
        if (file.size > 5 * 1024 * 1024) {
            form.errors.profile_img = 'File size should not exceed 5MB';
            return;
        }

        // Kiểm tra định dạng file
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            form.errors.profile_img = 'Only JPEG, PNG, and GIF files are allowed';
            return;
        }

        // Giải phóng URL preview cũ
        if (previewImage.value) {
            URL.revokeObjectURL(previewImage.value);
        }

        // Tạo URL preview mới
        previewImage.value = URL.createObjectURL(file);

        // Đặt file vào form
        form.profile_img = file;
        form.errors.profile_img = null; // Clear lỗi
    }
};

const removeAvatar = () => {
    if (previewImage.value) {
        URL.revokeObjectURL(previewImage.value);
        previewImage.value = null;
    }

    form.profile_img = null;
    if (fileInput.value) {
        fileInput.value.value = ''; // Reset file input
    }
};

// Hàm kích hoạt input file
const triggerFileInput = () => {
    fileInput.value.click();
};


// Computed để xác định src của ảnh
const avatarSrc = computed(() => {
    // Ưu tiên preview image (ảnh mới chọn)
    if (previewImage.value) {
        return previewImage.value;
    }
    
    // Nếu không có preview, sử dụng ảnh từ user
    if (user.profile_img) {
        return `/profile/${user.profile_img}`;
    }
    
    // Nếu không có ảnh, sử dụng ảnh mặc định
    return '/uploads/profile/default-profile.png';
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-xl font-medium text-gray-900">Profile Information</h2>
            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information, email address, and avatar.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div class="mb-4 mt-4 flex flex-col items-center">
                <InputLabel for="avatar" value="" class="mb-2 text-lg font-semibold text-gray-700" />
                <div class="relative">
                    <img
                        :src="avatarSrc"
                        alt="Avatar"
                        class="w-40 h-40 rounded-full object-cover border-4 border-blue-500 shadow-lg"
                    />
                    <!-- Avatar actions -->
                    <div class="absolute bottom-0 right-0 flex space-x-2">
                        <!-- Upload button -->
                        <button 
                            type="button" 
                            @click="triggerFileInput"
                            class="w-8 h-8 bg-blue-500 border-2 border-white rounded-full flex items-center justify-center shadow"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </button>
                        
                        <!-- Remove button (only show if there's a custom avatar) -->
                        <button 
                            v-if="user.profile_img || previewImage"
                            type="button" 
                            @click="removeAvatar"
                            class="w-8 h-8 bg-red-500 border-2 border-white rounded-full flex items-center justify-center shadow"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Hidden file input -->
                <input 
                    type="file" 
                    ref="fileInput"
                    @change="handleFileUpload"
                    accept="image/jpeg,image/png,image/gif"
                    class="hidden"
                />
                
                <InputError class="mt-2 text-red-500" :message="form.errors.profile_img" />
            </div>

            <div>
                <InputLabel for="name" value="Name" />
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

            <div>
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

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div>
                  <InputLabel for="class" value="Class" />
                  <TextInput
                      id="class"
                      type="text"
                      class="mt-1 block w-full"
                      v-model="form.class"
                      autocomplete="class"
                  />
                  <InputError class="mt-2" :message="form.errors.class" />
              </div>

            <div>
                <InputLabel for="student_code" value="Student Code" />
                <TextInput
                    id="student_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.student_code"
                    autocomplete="student_code"
                />
                <InputError class="mt-2" :message="form.errors.student_code" />
            </div>

            <div>
                <InputLabel for="phone" value="Phone" />
                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    autocomplete="phone"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>


            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

