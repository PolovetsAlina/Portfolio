<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ page import="java.sql.Connection" %>
<%@ page import="java.sql.DriverManager" %>
<%@ page import="java.sql.PreparedStatement" %>
<%@ page import="java.sql.SQLException" %>

<%
    HttpSession session = request.getSession(false);
    if (session == null || session.getAttribute("username") == null) {
        response.sendRedirect("index.jsp");
        return;
    }
    
    String username = (String) session.getAttribute("username");
    String role = (String) session.getAttribute("role");
    if (!"admin".equals(role)) {
        response.sendRedirect("area_riservata.jsp");
        return;
    }

    String code = request.getParameter("code");

    try {
        Class.forName("com.mysql.cj.jdbc.Driver");
        Connection conn = DriverManager.getConnection("jdbc:mysql://localhost:3306/cinema", "root", "");
        
        String sql = "DELETE FROM film WHERE Codice_film = ?";
        PreparedStatement stmt = conn.prepareStatement(sql);
        stmt.setString(1, code);
        
        int rows = stmt.executeUpdate();
        
        if (rows > 0) {
            response.sendRedirect("area_amministratore.jsp");
        } else {
            out.println("Errore nell'eliminazione del film.");
        }
        
        stmt.close();
        conn.close();
    } catch (Exception e) {
        out.println("Errore: " + e.getMessage());
    }
%>
