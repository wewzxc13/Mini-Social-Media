'use client';

import React, { useState } from 'react';
import axios from 'axios';
import { useRouter } from 'next/navigation';

const Login = () => {
  const router = useRouter();
  const [schoolId, setSchoolId] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      const response = await axios.post('http://localhost/appDB/login.php', {
        school_id: schoolId,
        password,
      });

      console.log('Login response:', response.data); 

      if (response.data.success) {
       
        localStorage.setItem('user', JSON.stringify(response.data.user));

        const { acctype_id } = response.data.user;
        if (acctype_id === 1) {
          router.push('/admin_dashboard');
        } else if (acctype_id === 2) {
          router.push('/student_dashboard');
        }
      } else {
        setError(response.data.message);
      }
    } catch (err) {
      console.error('Login error:', err);
      if (err.response) {
        setError(err.response.data.message || 'An unexpected error occurred.');
      } else if (err.request) {
        setError('No response received from the server.');
      } else {
        setError('An unexpected error occurred. Please try again later.');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container">
      <h2>Login</h2>
      <form onSubmit={handleSubmit} className="form">
        <div className="inputGroup">
          <label htmlFor="schoolId">School ID: <span className="required">*</span></label>
          <input
            type="text"
            id="schoolId"
            value={schoolId}
            onChange={(e) => setSchoolId(e.target.value)}
            required
            className="input"
          />
        </div>
        <div className="inputGroup">
          <label htmlFor="password">Password: <span className="required">*</span></label>
          <div className="passwordWrapper">
            <input
              type={showPassword ? 'text' : 'password'}
              id="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              className="input"
            />
            <button
              type="button"
              onClick={() => setShowPassword(!showPassword)}
              className="showPasswordButton"
            >
              {showPassword ? 'Hide' : 'Show'}
            </button>
          </div>
        </div>
        {error && <p className="error">{error}</p>}
        <button type="submit" disabled={loading} className="button">
          {loading ? 'Logging in...' : 'Login'}
        </button>
      </form>
      <p className='text-center'>
        Don't have an account? <span onClick={() => router.push('/register')} className="link">Register</span>
      </p>
    </div>
  );
};

export default Login;
