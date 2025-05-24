<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.*" %>
<%
    String username = request.getParameter("username");
    if (username == null || username.isEmpty()) {
        response.sendRedirect("area_amministratore.jsp?error=Username non specificato");
        return;
    }

    Connection conn = null;
    PreparedStatement pstmt = null;
    
    try {
        Class.forName("com.mysql.cj.jdbc.Driver");
        conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
        
        String sql = "DELETE FROM utenti WHERE username = ?";
        pstmt = conn.prepareStatement(sql);
        pstmt.setString(1, username);
        int rowsAffected = pstmt.executeUpdate();
        
        if (rowsAffected > 0) {
            response.sendRedirect("area_amministratore.jsp?success=Utente eliminato con successo");
        } else {
            response.sendRedirect("area_amministratore.jsp?error=Utente non trovato");
        }
    } catch (Exception e) {
        response.sendRedirect("area_amministratore.jsp?error=" + e.getMessage());
    } finally {
        try { if (pstmt != null) pstmt.close(); } catch (Exception e) {}
        try { if (conn != null) conn.close(); } catch (Exception e) {}
    }
%>
