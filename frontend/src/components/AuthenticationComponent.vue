<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 font-sans p-4">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">

      <div v-if="currentView !== 'verify'">
        <div class="flex mb-6 rounded-md overflow-hidden border border-gray-300">
          <button @click="setView('login')" :class="[
            'flex-1 p-3 font-semibold text-center transition-colors duration-200',
            currentView === 'login' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]">
            Sign In
          </button>
          <button @click="setView('signup')" :class="[
            'flex-1 p-3 font-semibold text-center transition-colors duration-200',
            currentView === 'signup' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]">
            Sign Up
          </button>
        </div>

        <form v-if="currentView === 'login'" @submit.prevent="handleLogin">
          <h2 class="text-center text-2xl font-semibold text-gray-800 mb-6">Welcome Back</h2>
          <div class="mb-4">
            <label for="login-email" class="block mb-2 text-sm font-semibold text-gray-700">Email</label>
            <input id="login-email" type="email" v-model="loginForm.email" required placeholder="you@example.com"
              class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20" />
          </div>
          <div class="mb-6">
            <label for="login-password" class="block mb-2 text-sm font-semibold text-gray-700">Password</label>
            <input id="login-password" type="password" v-model="loginForm.password" required placeholder="••••••••"
              class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20" />
          </div>
          <button type="submit" :disabled="isLoading"
            class="w-full py-3 text-base font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
            {{ isLoading ? 'Signing In...' : 'Sign In' }}
          </button>
        </form>

        <form v-else-if="currentView === 'signup'" @submit.prevent="handleSignup">
          <h2 class="text-center text-2xl font-semibold text-gray-800 mb-6">Create Your Account</h2>
          <div class="mb-4">
            <label for="signup-name" class="block mb-2 text-sm font-semibold text-gray-700">Name</label>
            <input id="signup-name" type="text" v-model="signupForm.name" required placeholder="Your Name"
              class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20" />
          </div>
          <div class="mb-4">
            <label for="signup-email" class="block mb-2 text-sm font-semibold text-gray-700">Email</label>
            <input id="signup-email" type="email" v-model="signupForm.email" required placeholder="you@example.com"
              class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20" />
          </div>
          <div class="mb-6">
            <label for="signup-password" class="block mb-2 text-sm font-semibold text-gray-700">Password</label>
            <input id="signup-password" type="password" v-model="signupForm.password" required
              placeholder="Create a strong password"
              class="w-full px-4 py-3 text-base border border-gray-300 rounded-md focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20" />
          </div>
          <button type="submit" :disabled="isLoading"
            class="w-full py-3 text-base font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
            {{ isLoading ? 'Creating Account...' : 'Sign Up' }}
          </button>
        </form>
      </div>

      <div v-else-if="currentView === 'verify'">
        <form @submit.prevent="handleVerification">
          <h2 class="text-center text-2xl font-semibold text-gray-800 mb-4">Check Your Email</h2>
          <p class="text-center leading-relaxed text-gray-600 mb-6">
            We've sent a 6-digit verification code to
            <strong class="font-semibold text-gray-800">{{ emailForVerification }}</strong>.
          </p>
          <div class="mb-6">
            <label for="code" class="block mb-2 text-sm font-semibold text-gray-700">Verification Code</label>
            <input id="code" type="text" v-model="verificationCode" required placeholder="123456" maxlength="6"
              class="w-full px-4 py-3 text-base text-center tracking-[0.5em] border border-gray-300 rounded-md focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20" />
          </div>
          <button type="submit" :disabled="isLoading"
            class="w-full py-3 text-base font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
            {{ isLoading ? 'Verifying...' : 'Verify and Sign In' }}
          </button>
          <a href="#" @click.prevent="setView('login')"
            class="block text-center mt-6 text-blue-600 font-semibold hover:underline">
            &larr; Back to Sign In
          </a>
        </form>
      </div>

      <div v-if="errorMessage"
        class="w-full p-4 mt-6 rounded-md text-center font-medium bg-red-100 text-red-700 border border-red-200">
        {{ errorMessage }}
      </div>
      <div v-if="successMessage"
        class="w-full p-4 mt-6 rounded-md text-center font-medium bg-green-100 text-green-700 border border-green-200">
        {{ successMessage }}
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
// Manages which view is active: 'login', 'signup', or 'verify'
const currentView = ref<'login' | 'signup' | 'verify'>('login');

// Form data
const loginForm = ref({ email: '', password: '' });
const signupForm = ref({ name: '', email: '', password: '' });
const verificationCode = ref('');

// State helpers
const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
// Store the email to know who to verify
const emailForVerification = ref('');

/**
 * Clears all messages and resets form data.
 */
function clearMessages() {
  errorMessage.value = '';
  successMessage.value = '';
}

/**
 * Switch the view between 'login' and 'signup'
 */
function setView(view: 'login' | 'signup') {
  clearMessages();
  currentView.value = view;
  // Reset form fields
  loginForm.value = { email: '', password: '' };
  signupForm.value = { name: '', email: '', password: '' };
}

/**
 * Handles the Sign Up form submission.
 * Your backend should create the user (in an unverified state)
 * and send the verification email.
 */
async function handleSignup() {
  isLoading.value = true;
  clearMessages();

  // API Call
  console.log('Signing up with:', signupForm.value);
  await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate API call

  try {
    // const response = await fetch('/api/register', {
    //   method: 'POST',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify(signupForm.value),
    // });

    // if (!response.ok) {
    //   const errorData = await response.json();
    //   throw new Error(errorData.message || 'Failed to sign up.');
    // }

    // --- On Success ---
    // Store the email for the verification step
    emailForVerification.value = signupForm.value.email;
    // Show the verification code input
    currentView.value = 'verify';
    successMessage.value = 'Account created! Please check your email for a verification code.';

  } catch (error: any) {
    errorMessage.value = error.message;
  } finally {
    isLoading.value = false;
  }
}

/**
 * Handles the Sign In form submission.
 * Your backend should validate credentials AND send the 2FA code.
 * It should NOT return a session token yet.
 */
async function handleLogin() {
  isLoading.value = true;
  clearMessages();

  // TODO: Replace with your actual API call
  console.log('Logging in with:', loginForm.value);
  await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate API call

  try {
    // const response = await fetch('/api/login', {
    //   method: 'POST',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify(loginForm.value),
    // });

    // if (!response.ok) {
    //   const errorData = await response.json();
    //   throw new Error(errorData.message || 'Invalid email or password.');
    // }

    // --- On Success ---
    // Store the email for the verification step
    emailForVerification.value = loginForm.value.email;
    // Show the verification code input
    currentView.value = 'verify';
    successMessage.value = 'Login successful! Please check your email for your 2FA code.';

  } catch (error: any) {
    errorMessage.value = error.message;
  } finally {
    isLoading.value = false;
  }
}

/**
 * Handles the final verification step.
 * Your backend should validate the code for the given email.
 * If successful, it should mark the user as "verified" (for signup)
 * and return the final session token/cookie (for both).
 */
async function handleVerification() {
  isLoading.value = true;
  clearMessages();

  // TODO: Replace with your actual API call
  console.log('Verifying code:', verificationCode.value, 'for user:', emailForVerification.value);
  await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate API call

  try {
    // const response = await fetch('/api/verify-code', {
    //   method: 'POST',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify({
    //     email: emailForVerification.value,
    //     code: verificationCode.value
    //   }),
    // });

    // if (!response.ok) {
    //   const errorData = await response.json();
    //   throw new Error(errorData.message || 'Invalid or expired code.');
    // }

    // const userData = await response.json();
    // e.g., { user: { ... }, token: '...' }

    // --- On Final Success ---
    // Save the token, update pinia/vuex store, etc.
    // localStorage.setItem('token', userData.token);

    // For this demo, just alert and reset
    alert('Success! You are now fully authenticated.');

    // Send user to the dashboard or reset the form
    // router.push('/dashboard');
    setView('login');
    verificationCode.value = '';
    emailForVerification.value = '';

  } catch (error: any) {
    errorMessage.value = error.message;
  } finally {
    isLoading.value = false;
  }
}
</script>

<style lang="">
  /* This block is intentionally empty because all styles are handled by Tailwind CSS */
</style>
